labels = [
  { title: 'prioridade-normal', color: '#64748B', description: 'Atendimento padrão' },
  { title: 'prioridade-alta', color: '#F97316', description: 'Requer atenção prioritária' },
  { title: 'prioridade-critica', color: '#DC2626', description: 'Situação urgente ou crítica' },
  { title: 'origem-email', color: '#F97316', description: 'Recebido por e-mail' },
  { title: 'origem-whatsapp', color: '#22C55E', description: 'Recebido por WhatsApp' },
  { title: 'origem-instagram', color: '#E1306C', description: 'Recebido pelo Instagram' },
  { title: 'origem-facebook', color: '#1877F2', description: 'Recebido pelo Facebook' },
  { title: 'origem-site', color: '#0EA5E9', description: 'Recebido pelo portal/site' },
  { title: 'origem-telefone', color: '#A855F7', description: 'Recebido por ligação telefônica' },
  { title: 'origem-telegram', color: '#229ED9', description: 'Recebido pelo Telegram' },
  { title: 'status-aguardando-resposta', color: '#F59E0B', description: 'Aguardando resposta do solicitante' },
  { title: 'status-em-atendimento', color: '#2563EB', description: 'Atendimento em andamento' },
  { title: 'status-encaminhado', color: '#7C3AED', description: 'Encaminhado para outro setor' },
  { title: 'status-aguardando-setor', color: '#FB923C', description: 'Aguardando ação do setor responsável' },
  { title: 'status-concluido', color: '#16A34A', description: 'Atendimento finalizado' },
  { title: 'status-cancelado', color: '#DC2626', description: 'Atendimento cancelado' },
  { title: 'org-sertao-digital', color: '#2563EB', description: 'Centro de Inovação e Tecnologia Sertão Digital' },
  { title: 'org-fundacao-cidade-digital', color: '#0EA5E9', description: 'Fundação Cidade Digital' },
  { title: 'org-academia-inovacoes', color: '#7C3AED', description: 'Academia de Inovações' },
  { title: 'org-colab-open', color: '#16A34A', description: 'Comunidade Colab Open' },
  { title: 'org-raiz-tech', color: '#F97316', description: 'Núcleo RaiZ Tech' },
  { title: 'projeto-plataforma360', color: '#2563EB', description: 'Plataforma360' },
  { title: 'projeto-veredas', color: '#16A34A', description: 'VEREDAS' },
  { title: 'projeto-ecidade', color: '#7C3AED', description: 'e-Cidade' },
  { title: 'projeto-ieducar', color: '#0891B2', description: 'i-Educar' },
  { title: 'projeto-amadeus-lms', color: '#F97316', description: 'Amadeus LMS' },
  { title: 'projeto-central-publica-digital', color: '#DC2626', description: 'Central Pública Digital' },
  { title: 'projeto-sigi-sd', color: '#0F172A', description: 'SIGI-SD' },
  { title: 'municipio-sousa', color: '#2563EB', description: 'Município de Sousa/PB' },
  { title: 'municipio-marizopolis', color: '#16A34A', description: 'Município de Marizópolis/PB' },
  { title: 'municipio-sao-joao-de-piranhas', color: '#F97316', description: 'Município de São João de Piranhas/PB' },
  { title: 'municipio-olinda', color: '#EC4899', description: 'Município de Olinda/PE' },
  { title: 'municipio-paulista', color: '#0891B2', description: 'Município de Paulista/PE' }
]

account = Account.find(1)

labels.each do |attrs|
  label = account.labels.find_or_initialize_by(title: attrs[:title])
  label.description = attrs[:description]
  label.color = attrs[:color]
  label.show_on_sidebar = true
  label.save!

  puts [label.title, label.color, label.description, label.show_on_sidebar].join('|')
end
