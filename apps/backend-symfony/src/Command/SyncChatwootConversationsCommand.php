<?php

namespace App\Command;

use App\Repository\Integration\Chatwoot\ChatwootAccountRepository;
use App\Service\Integration\Chatwoot\ChatwootApiClient;
use App\Service\Integration\Chatwoot\ChatwootConversationSyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sigi:chatwoot:sync',
    description: 'Sincroniza conversas recentes do Chatwoot e gera protocolos SIGI.',
)]
final class SyncChatwootConversationsCommand extends Command
{
    public function __construct(
        private readonly ChatwootApiClient $apiClient,
        private readonly ChatwootConversationSyncService $syncService,
        private readonly ChatwootAccountRepository $accountRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Quantidade maxima de conversas recentes.', 50)
            ->addOption('status', null, InputOption::VALUE_REQUIRED, 'Status a consultar no Chatwoot.', 'all')
            ->addOption('no-note', null, InputOption::VALUE_NONE, 'Nao envia nota privada com o protocolo.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $account = $this->accountRepository->createAdminListQueryBuilder()
            ->andWhere('account.isActive = true')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $limit = max(1, (int) $input->getOption('limit'));
        $status = (string) $input->getOption('status');
        $sendNote = !$input->getOption('no-note');
        $synced = 0;

        foreach ($this->apiClient->getRecentConversations($account, $limit, $status) as $conversation) {
            $protocol = $this->syncService->syncPayload($conversation, $account, $sendNote);
            if (null !== $protocol) {
                ++$synced;
                $io->writeln(sprintf('%s - conversa %s', $protocol->getProtocolCode(), $protocol->getChatwootConversationId()));
            }
        }

        $io->success(sprintf('%d conversa(s) sincronizada(s).', $synced));

        return Command::SUCCESS;
    }
}
