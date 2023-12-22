# Use a imagem base compatível com ARM64
FROM openjdk:11-jre-slim

# Instale o curl
RUN apt-get update && apt-get install -y curl && rm -rf /var/lib/apt/lists/*

# Crie o diretório para o Metabase
WORKDIR /app

# Faça o download do Metabase
RUN curl -o /app/metabase.jar https://downloads.metabase.com/v0.41.2/metabase.jar

# Expõe a porta que o Metabase usará
EXPOSE 3000

# Comando para iniciar o Metabase
CMD ["java", "-jar", "/app/metabase.jar"]