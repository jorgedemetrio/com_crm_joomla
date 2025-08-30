#!/bin/bash

# --- Configuração ---
# Edite esta variável para apontar para a raiz da sua instalação Joomla.
JOOMLA_PATH="/var/www/html/joomla"

# Nome da pasta do componente.
COMPONENT_FOLDER="com_crm_joomla"

# --- Validação ---
if [ ! -d "$JOOMLA_PATH" ]; then
  echo "Erro: O diretório do Joomla especificado não foi encontrado: $JOOMLA_PATH"
  echo "Por favor, edite a variável JOOMLA_PATH no script."
  exit 1
fi

# Caminhos dos links simbólicos a serem removidos
ADMIN_DEST_PATH="$JOOMLA_PATH/administrator/components/$COMPONENT_FOLDER"
SITE_DEST_PATH="$JOOMLA_PATH/components/$COMPONENT_FOLDER"

echo "Removendo o ambiente de desenvolvimento..."

# --- Remoção dos Links Simbólicos ---

if [ -L "$ADMIN_DEST_PATH" ]; then
  echo "Removendo link simbólico do backend de: $ADMIN_DEST_PATH"
  rm "$ADMIN_DEST_PATH"
else
  echo "Nenhum link simbólico do backend encontrado para remover."
fi

if [ -L "$SITE_DEST_PATH" ]; then
  echo "Removendo link simbólico do frontend de: $SITE_DEST_PATH"
  rm "$SITE_DEST_PATH"
else
  echo "Nenhum link simbólico do frontend encontrado para remover."
fi


echo ""
echo "Ambiente de desenvolvimento removido com sucesso!"
echo "Você pode agora instalar o componente via ZIP normalmente, se desejar."
