account_id = ENV.fetch('SIGI_CHATWOOT_ACCOUNT_ID', '1')
app_url = ENV.fetch('SIGI_ASSISTANT_APP_URL', 'http://admin.sigi.localhost/pt_BR/chatwoot/assistant')

account = Account.find(account_id)
user = account.users.order(:id).first || User.order(:id).first

raise 'Nenhum usuario encontrado para vincular o dashboard app.' if user.nil?

dashboard_app = account.dashboard_apps.find_or_initialize_by(title: 'Assistente SIGI')
dashboard_app.user = user
dashboard_app.content = [
  {
    type: 'frame',
    url: app_url
  }
]
dashboard_app.save!

puts "Assistente SIGI registrado em #{app_url}"
