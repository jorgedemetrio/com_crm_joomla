#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

# --- Validation ---
# Check if version and component name are provided as arguments.
if [ -z "$1" ] || [ -z "$2" ]; then
  echo "Erro: A versão e o nome do componente não foram fornecidos."
  echo "Uso: $0 <versao> <nome_componente>"
  exit 1
fi

VERSION=$1
APP_NAME=$2 # e.g., com_crm_joomla
SOURCE_DIR="$APP_NAME"
TIPO="component" # Hardcoded for this project

# --- Configuration ---
# Temporary build directory.
BUILD_DIR="build_temp"

# Remove 'v' prefix if it exists (e.g., v1.2.3 -> 1.2.3).
PLAIN_VERSION=${VERSION#v}

# Final ZIP file name.
ZIP_FILE="${APP_NAME}-${PLAIN_VERSION}.zip"

# --- Logging ---
echo "Starting deploy process for ${APP_NAME}, version ${PLAIN_VERSION}"

# --- Cleanup and Preparation ---
echo "Cleaning up previous build directories..."
rm -rf ${BUILD_DIR}
mkdir -p ${BUILD_DIR}

echo "Copying component files to the build directory..."
# We need to copy the whole component folder into the build dir
cp -r ${SOURCE_DIR}/* ${BUILD_DIR}/

# --- Version Update in XML ---
echo "Updating version in ${BUILD_DIR}/${APP_NAME}.xml to ${PLAIN_VERSION}..."
# The manifest is in the root of the component folder
sed -i "s|<version>.*</version>|<version>${PLAIN_VERSION}</version>|g" "${BUILD_DIR}/${APP_NAME}.xml"
echo "Version updated successfully."

# --- ZIP Package Generation ---
echo "Creating installation package: ${ZIP_FILE}"
cd ${BUILD_DIR}
zip -r ../${ZIP_FILE} . -x "*.git*"
cd ..
echo "ZIP package created successfully at $(pwd)/${ZIP_FILE}"

# --- Update XML Entry Generation ---
echo "Generating new update entry..."
cat > nova_entrada.xml << EOL
    <update>
        <name>${APP_NAME}</name>
        <element>${APP_NAME}</element>
        <type>${TIPO}</type>
        <version>${PLAIN_VERSION}</version>
        <infourl title="Sobieski Produções">https://apps.sobieskiproducoes.com.br/${APP_NAME}/atualizacao.xml</infourl>
        <downloads>
            <downloadurl type="full" format="zip">https://apps.sobieskiproducoes.com.br/${APP_NAME}/${ZIP_FILE}</downloadurl>
        </downloads>
        <tags>
            <tag>stable</tag>
        </tags>
        <maintainer>Jorge Demetrio</maintainer>
        <maintainerurl>https://www.sobieskiproducoes.com.br</maintainerurl>
        <targetplatform name="joomla" version="5.*"/>
        <php_minimum>8.1</php_minimum>
    </update>
EOL
echo "New update entry generated in nova_entrada.xml."

# --- Update XML Combination ---
echo "Preparing final atualizacao.xml file..."
# Attempt to download the existing update file via wget.
FILE_EXISTS=true
echo "Attempting to download existing atualizacao.xml from https://apps.sobieskiproducoes.com.br/${APP_NAME}/atualizacao.xml..."
wget -q -O atualizacao_remota.xml "https://apps.sobieskiproducoes.com.br/${APP_NAME}/atualizacao.xml" || FILE_EXISTS=false

if [ "$FILE_EXISTS" = false ]; then
  echo "atualizacao.xml not found on server. Creating a new one."
  (
    echo '<?xml version="1.0" encoding="utf-8"?>'
    echo '<updates>'
    cat nova_entrada.xml
    echo '</updates>'
  ) > atualizacao.xml
else
  echo "atualizacao.xml found. Adding new entry."
  # Insert the new entry after the <updates> tag
  sed '2r nova_entrada.xml' atualizacao_remota.xml > atualizacao.xml
fi
echo "Final atualizacao.xml file generated successfully."

# --- Deploy via SFTP ---
echo "Starting deploy to SFTP server..."
# Check if FTP environment variables are set.
if [ -z "$FTP_URL" ] || [ -z "$FTP_USER" ] || [ -z "$FTP_PASSWORD" ]; then
    echo "Error: FTP_URL, FTP_USER, and FTP_PASSWORD environment variables must be set."
    exit 1
fi
lftp -c "set sftp:auto-confirm yes; set ftp:ssl-allow yes; set ssl:verify-certificate no;
open -u ${FTP_USER},${FTP_PASSWORD} ${FTP_URL};
mkdir -p /${APP_NAME};
cd /${APP_NAME};
put -O . ${ZIP_FILE};
put -O . atualizacao.xml;
bye"
echo "Deploy completed successfully!"

# --- Final Cleanup ---
echo "Cleaning up temporary files..."
rm -rf ${BUILD_DIR}
rm ${ZIP_FILE}
rm nova_entrada.xml
rm -f atualizacao.xml atualizacao_remota.xml

echo "Process finished."
