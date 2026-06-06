# rag-knowledge-base

## Quando usar

Use para criar base de conhecimento com Qdrant e Ollama.

## Entrada esperada

- Fonte dos documentos.
- Dominio de conhecimento.
- Politica de atualizacao.

## Saida esperada

- Estrutura de colecao.
- Processo de chunking.
- Processo de embeddings.
- Consulta RAG documentada.

## Passos

- Normalizar documentos.
- Gerar chunks.
- Criar embeddings.
- Persistir no Qdrant.
- Consultar contexto antes de gerar resposta.

## Checklist

- Fontes sao registradas?
- Dados pessoais foram tratados?
- Resposta inclui contexto verificavel?

## Exemplo de prompt

Crie uma base RAG para perguntas frequentes da Central Publica Digital.
