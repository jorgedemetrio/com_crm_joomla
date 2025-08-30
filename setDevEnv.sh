#!/bin/bash

# --- Configuração ---
# Edite esta variável para apontar para a raiz da sua instalação Joomla.
JOOMLA_PATH="/var/www/html/joomla"

# Nome da pasta do componente no repositório.
COMPONENT_FOLDER="com_crm_joomla"

# --- Validação ---
if [ ! -d "$JOOMLA_PATH" ]; then
  echo "Erro: O diretório do Joomla especificado não foi encontrado: $JOOMLA_PATH"
  echo "Por favor, edite a variável JOOMLA_PATH no script."
  exit 1
fi

# Caminhos de destino dentro da instalação do Joomla
ADMIN_DEST_PATH="$JOOMLA_PATH/administrator/components/$COMPONENT_FOLDER"
SITE_DEST_PATH="$JOOMLA_PATH/components/$COMPONENT_FOLDER"

# Caminhos de origem no repositório
ADMIN_SOURCE_PATH="$(pwd)/$COMPONENT_FOLDER/administrator"
SITE_SOURCE_PATH="$(pwd)/$COMPONENT_FOLDER/site"

echo "Configurando o ambiente de desenvolvimento..."

# --- Criação dos Links Simbólicos ---

# 1. Backend (Administrator)
echo "Removendo o diretório de administrador existente (se houver)..."
rm -rf "$ADMIN_DEST_PATH"
echo "Criando link simbólico para o backend em: $ADMIN_DEST_PATH"
ln -s "$ADMIN_SOURCE_PATH" "$ADMIN_DEST_PATH"

# 2. Frontend (Site)
echo "Removendo o diretório de frontend existente (se houver)..."
rm -rf "$SITE_DEST_PATH"
echo "Criando link simbólico para o frontend em: $SITE_DEST_PATH"
ln -s "$SITE_SOURCE_PATH" "$SITE_DEST_PATH"

echo ""
echo "Ambiente de desenvolvimento configurado com sucesso!"
echo "Lembre-se de usar a função 'Descobrir' no instalador do Joomla para instalar o componente."
