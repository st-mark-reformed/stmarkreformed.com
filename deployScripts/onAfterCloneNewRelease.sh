#!/bin/bash
#
# ${1} = env
# ${2} = {{release}}
# ${3} = {{project}}

# Symlink to persistent storage
dirs=(
    "storage"
    "public/imagecache"
    "public/uploads"
);

for i in "${dirs[@]}" ; do
    rm -rf ${2}/${i};
    mkdir -p ${3}/storage/${i};
    ln -sf ${3}/storage/${i} ${2}/${i};
    sudo chmod -R 0777 ${3}/storage/${i};
done;

files=(
    ".env"
    "config/license.key"
);

for i in "${files[@]}" ; do
    rm -rf ${2}/${i};
    ln -sf ${3}/storage/${i} ${2}/${i};
done;

# Symlink everything in storage/public (crazy but we have a lot of weird stuff)
for f in ${3}/storage/public/*; do
    # Get the file name
    FILENAME=$(basename "$f" .deb);

    rm -rf ${2}/public/${FILENAME};
    ln -s ${3}/storage/public/${FILENAME} ${2}/public/${FILENAME};
done;

# Create other storage directories as needed
dirs=(
    "storage"
);
for i in "${dirs[@]}" ; do
    rm -rf ${2}/${i};
    mkdir -p ${3}/storage/${i};
    ln -sf ${3}/storage/${i} ${2}/${i};
    sudo chmod -R 0777 ${3}/storage/${i};
done

# Update asset versioning
timestamp=$(date +%s);
cp ${2}/public/assets/css/style.min.css ${2}/public/assets/css/style.min.${timestamp}.css;
cp ${2}/public/assets/js/script.min.js ${2}/public/assets/js/script.min.${timestamp}.js;
sed -i -e "s/'staticAssetCacheTime' => ''/'staticAssetCacheTime' => $timestamp/g" ${2}/config/general.php;

# Update file permissions
sudo chmod -R 0777 ${2}/public/cache;
sudo chmod -R 0777 ${2}/public/cpresources;
sudo chmod -R 0777 ${2}/config;

# Fix a cache issue that prevents Envoyer from deleting old releases
for f in ${3}/releases/*; do
    if [[ -d "${f}/public/cache" ]]; then
        sudo chmod -R 0777 ${f}/public/cache;
    fi
    if [[ -d "${f}/public/cpresources" ]]; then
        sudo chmod -R 0777 ${f}/public/cpresources;
    fi
done;
