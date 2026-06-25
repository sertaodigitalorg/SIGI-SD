<?php

namespace App\Service\Integration\Chatwoot;

use App\Entity\ContactType;
use App\Entity\Person;
use App\Entity\PersonContact;
use App\Repository\ContactTypeRepository;
use App\Repository\PersonContactRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ChatwootContactSyncService
{
    public function __construct(
        private PersonRepository $personRepository,
        private PersonContactRepository $personContactRepository,
        private ContactTypeRepository $contactTypeRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function sync(ChatwootConversationData $data): ?Person
    {
        if (null === $data->contactId && null === $data->contactName && null === $data->contactHandle && null === $data->phone && null === $data->username) {
            return null;
        }

        $email = $data->email ?? $this->extractEmail($data->contactHandle);
        $phone = $data->phone ?? $this->extractPhone($data->contactHandle);
        $username = $this->normalizeUsername($data->username);
        $person = $this->findPerson($data->contactId, $email, $phone, $username);

        if (null === $person) {
            $person = (new Person())
                ->setFullName($data->contactName ?: ($email ?? $phone ?? $username ?? 'Contato Chatwoot'))
                ->setPersonType(Person::TYPE_UNKNOWN)
                ->setSource('chatwoot');

            $this->entityManager->persist($person);
        }

        if (null !== $data->contactName && '' !== trim($data->contactName)) {
            $person->setFullName($data->contactName);
        }

        if (null !== $data->contactId) {
            $person->setChatwootContactId($data->contactId);
        }

        if (null !== $email && null === $person->getPrimaryEmail()) {
            $person->setPrimaryEmail($email);
        }

        if (null !== $phone && null === $person->getPrimaryPhone()) {
            $person->setPrimaryPhone($phone);
        }

        $person->setUpdatedAt(new \DateTimeImmutable());
        $this->syncPersonContact($person, 'E-mail', $email);
        $this->syncPersonContact($person, 'Telefone', $phone);
        $this->syncPersonContact($person, 'WhatsApp', ChatwootChannelMapper::CHANNEL_WHATSAPP === $data->channel ? $phone : null);
        $this->syncPersonContact($person, 'Instagram', ChatwootChannelMapper::CHANNEL_INSTAGRAM === $data->channel ? $username : null);

        return $person;
    }

    private function findPerson(?string $chatwootContactId, ?string $email, ?string $phone, ?string $username): ?Person
    {
        if (null !== $chatwootContactId) {
            $person = $this->personRepository->findOneByChatwootContactId($chatwootContactId);
            if (null !== $person) {
                return $person;
            }
        }

        if (null !== $email) {
            $person = $this->personRepository->findOneByPrimaryEmail($email);
            if (null !== $person) {
                return $person;
            }
        }

        if (null !== $phone) {
            $person = $this->personRepository->findOneByPrimaryPhone($phone);
            if (null !== $person) {
                return $person;
            }
        }

        if (null !== $username) {
            return $this->personContactRepository->findOneByValue($username)?->getPerson();
        }

        return null;
    }

    private function syncPersonContact(Person $person, string $contactTypeName, ?string $value): void
    {
        if (null === $value) {
            return;
        }

        $contactType = $this->contactTypeRepository->findOneBy(['name' => $contactTypeName]);
        if (!$contactType instanceof ContactType) {
            return;
        }

        foreach ($this->personContactRepository->findBy(['person' => $person, 'contactType' => $contactType]) as $contact) {
            if ($contact instanceof PersonContact && 0 === strcasecmp((string) $contact->getValue(), $value)) {
                return;
            }
        }

        $contact = (new PersonContact())
            ->setPerson($person)
            ->setContactType($contactType)
            ->setValue($value)
            ->setLabel('Chatwoot')
            ->setIsPrimary(true);

        $this->entityManager->persist($contact);
    }

    private function extractEmail(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        $value = trim($value);

        return false === filter_var($value, FILTER_VALIDATE_EMAIL) ? null : mb_strtolower($value);
    }

    private function extractPhone(?string $value): ?string
    {
        if (null === $value || false !== filter_var(trim($value), FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value);

        return is_string($digits) && strlen($digits) >= 8 ? $digits : null;
    }

    private function normalizeUsername(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        $value = trim($value);
        if ('' === $value || false !== filter_var($value, FILTER_VALIDATE_EMAIL) || preg_match('/^\+?[\d\s().-]+$/', $value)) {
            return null;
        }

        return ltrim($value, '@');
    }
}
