# notificacoes-slack
Projeto criado para enviar notificações para o slack

## Instalação

### Primeiro passo
Execute o comando `composer update` para baixar as bibliotecas requeridas no arquivo "composer.json".

### Segundo passo
Crie o banco de dados o nome desejado e execute o script abaixo:

```sql
CREATE TABLE tb_messages (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  update_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  url varchar(255) DEFAULT NULL,
  channel varchar(80) DEFAULT NULL,
  username varchar(80) DEFAULT NULL,
  icon_url varchar(120) DEFAULT NULL,
  text longtext CHARACTER SET utf8mb4 NOT NULL,
  sent_at timestamp NULL DEFAULT NULL,
  schedule_for timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
```

### Terceiro passo
Crie um configure o arquivo constants.php. Siga o exemplo do arquivo "constants.example.php". 

## WebHooks
Para criar WebHooks de entrada, acesse https://my.slack.com/services/new/incoming-webhook.

## Requisições

### Criar mensagem
```json
{
	"url": "https://hooks.slack.com/services/xxxxx/xxxxx/xxxxx",
	"channel": "#nomecanal",
	"username": "Nome do Usuário. Ex: Meu Robô",
	"icon_url": "https://softwarecriativo.com.br/ciborgue.png",
	"text": "Mensagem de teste",
	"schedule_for": null
}
```

### Enviar mensagens
```json
{
	"per_second": 5
}
```

### Possíveis erros
Status code: `404`
```json
{
	"success": false,
	"error": "404 - URI não encontrada!"
}
```
