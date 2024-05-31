#!/bin/bash

chemin="/work"

# Fonction pour mettre à jour le numéro de version
update_version() {
  local major minor build
  IFS='.' read -r major minor build <<< "$1"
  
  case $2 in
    "--major")
      ((major++))
      minor=0
      build=0
      ;;
    "--minor")
      ((minor++))
      build=0
      ;;
    "--build")
      ((build++))
      ;;
    *)
      echo "Paramètre invalide. Utilisez '--major', '--minor' ou '--build'."
      exit 1
      ;;
  esac

  echo "$major.$minor.$build"
}

# Récupération du paramètre
if [ "$#" -ne 1 ]; then
  echo "Usage: $0 [--major | --minor | --build]"
  exit 1
fi

parametre="$1"

# Mise à jour du numéro de version dans le fichier config
version_actuelle=$(grep 'VERSION=' config | awk -F'=' '{print $2}')
nouvelle_version=$(update_version "$version_actuelle" "$parametre")
sed -i "s/VERSION=$version_actuelle/VERSION=$nouvelle_version/" config

echo "Nouvelle version: $nouvelle_version"

#recuperer le numéro de version depuis le fichier config 
version=$(grep 'VERSION=' config | awk -F'=' '{print $2}')
echo REGARDE $version

# Création du volume sae103
docker volume create sae103

# Lancement du conteneur clock en mode détaché avec le volume sae103 monté
docker run -d --name sae103-forever -v sae103:$chemin clock

# Copie des fichiers .c dans le volume sae103 en utilisant sae103-forever comme conteneur cible
docker cp config sae103-forever:$chemin
docker cp doc-utilisateur.md sae103-forever:$chemin
for fichier_c in fichiers_sources/*.c; do
    docker cp "$fichier_c" sae103-forever:$chemin
done

docker cp gendoc-tech.php sae103-forever:$chemin
docker cp gendoc-user.php sae103-forever:$chemin

# Lancement de tous les autres traitements les uns après les autres, en mode non interactif
docker run --rm -v sae103:$chemin sae103-php php gendoc-tech.php
docker run --rm -v sae103:$chemin sae103-php php gendoc-user.php

# a changer DOC_TECH-!!2.7.4!!.html pour avoir la version du fichier automatiquement
docker run --rm -v sae103:$chemin sae103-html2pdf "html2pdf DOC_TECH-$version.html DOC_TECH-$version.pdf"
docker run --rm -v sae103:$chemin sae103-html2pdf "html2pdf DOC_USER-$version.html DOC_USER-$version.pdf"

docker run --rm -v sae103:$chemin sae103-php tar -czvf Documentations.tar.gz "DOC_TECH-$version.pdf" "DOC_USER-$version.pdf"

#ls le volume
docker run --rm -v sae103:$chemin sae103-php ls -lt ./

# Récupération de l'archive finale depuis le volume sae103, en utilisant sae103-forever comme conteneur source

docker cp sae103-forever:$chemin/Documentations.tar.gz ./

# Arrêt du conteneur sae103-forever
docker stop sae103-forever
docker rm sae103-forever

# Suppression du volume sae103
docker volume rm sae103