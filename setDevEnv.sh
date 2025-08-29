#!/bin/bash


if [ -z "$1" ]; then
  echo "Deve infomar a pasta de instalação do Joomla."
  exit 1
fi
if [ "$EUID" -ne 0 ]; then
  echo "Este script precisa ser executado como root ou com sudo."
  exit 1
fi



PATH_JOOMLA=$1
# Ex: "/var/www/html/hoteis"
LOCAL=$(pwd)
LOCAL_COMPONENTE="${LOCAL}/com_crm_joomla"

rm -Rf "${PATH_JOOMLA}/media/com_crm_joomla"
ln -s "${LOCAL_COMPONENTE}/media/com_crm_joomla" "${PATH_JOOMLA}/media/com_crm_joomla"
chown -Rf www-data:www-data "${PATH_JOOMLA}/media/com_crm_joomla"
chmod -Rf a+wrx "${PATH_JOOMLA}/media/com_crm_joomla"



rm -Rf "${PATH_JOOMLA}/administrator/components/com_crm_joomla"
ln -s "${LOCAL_COMPONENTE}/administrator" "${PATH_JOOMLA}/administrator/components/com_crm_joomla"
chown -Rf www-data:www-data "${PATH_JOOMLA}/administrator/components/com_crm_joomla"
chmod -Rf a+wrx "${PATH_JOOMLA}/administrator/components/com_crm_joomla"



rm -Rf "${PATH_JOOMLA}/components/com_crm_joomla"
ln -s "${LOCAL_COMPONENTE}/site" "${PATH_JOOMLA}/components/com_crm_joomla"
chown -Rf www-data:www-data "${PATH_JOOMLA}/components/com_crm_joomla"
chmod -Rf a+wrx "${PATH_JOOMLA}/components/com_crm_joomla"



for LANG in en-GB pt-BR es-ES de-DE fr-FR zh-CN it-IT ja-JP; do
  mkdir -p "${PATH_JOOMLA}/language/${LANG}"
  rm -Rf "${PATH_JOOMLA}/language/${LANG}/${LANG}.com_crm_joomla.ini"
  ln -s "${LOCAL_COMPONENTE}/language/${LANG}/${LANG}.com_crm_joomla.ini" "${PATH_JOOMLA}/language/${LANG}/${LANG}.com_crm_joomla.ini"
  chown -Rf www-data:www-data "${PATH_JOOMLA}/language/${LANG}/${LANG}.com_crm_joomla.ini"
  chmod -Rf a+wrx "${PATH_JOOMLA}/language/${LANG}/${LANG}.com_crm_joomla.ini"
done


for LANG in en-GB pt-BR es-ES de-DE fr-FR zh-CN it-IT ja-JP; do
  mkdir -p "${PATH_JOOMLA}/administrator/language/${LANG}"
  rm -Rf "${PATH_JOOMLA}/administrator/language/${LANG}/${LANG}.com_crm_joomla.ini"
  rm -Rf "${PATH_JOOMLA}/administrator/language/${LANG}/${LANG}.com_crm_joomla.sys.ini"
  ln -s "${LOCAL_COMPONENTE}/administrator/language/${LANG}/${LANG}.com_crm_joomla.ini" "${PATH_JOOMLA}/administrator/language/${LANG}/${LANG}.com_crm_joomla.ini"
  ln -s "${LOCAL_COMPONENTE}/administrator/language/${LANG}/${LANG}.com_crm_joomla.sys.ini" "${PATH_JOOMLA}/administrator/language/${LANG}/${LANG}.com_crm_joomla.sys.ini"
  chown -Rf www-data:www-data "${PATH_JOOMLA}/administrator/language/${LANG}/${LANG}.com_crm_joomla.ini"
  chmod -Rf a+wrx "${PATH_JOOMLA}/administrator/language/${LANG}/${LANG}.com_crm_joomla.ini"
  chown -Rf www-data:www-data "${PATH_JOOMLA}/administrator/language/${LANG}/${LANG}.com_crm_joomla.sys.ini"
  chmod -Rf a+wrx "${PATH_JOOMLA}/administrator/language/${LANG}/${LANG}.com_crm_joomla.sys.ini"
done

