# RAG local

O RAG local combina documentos, embeddings, Qdrant e Ollama para responder com base na base de conhecimento da Central Publica Digital.

Fluxo recomendado:

- Catalogar documento.
- Gerar chunks.
- Criar embeddings.
- Persistir vetores no Qdrant.
- Consultar contexto relevante.
- Gerar resposta com Ollama.
- Registrar fontes usadas.
