#!/bin/bash
#
# ${1} = env
# ${2} = {{release}}
# ${3} = {{project}}

# Create upload storage directories as needed
for d in ${2}/public/uploads/*/ ; do
    # Get the directory name
    DIRNAME=$(basename "$d" .deb);

    # Create the directory in the persistent storage location
    mkdir -p ${3}/storage/public/uploads/${DIRNAME};
    cp ${2}/public/uploads/${DIRNAME}/.gitignore ${3}/storage/public/uploads/${DIRNAME}/;
    chmod -R 0777 ${3}/storage/public/uploads/${DIRNAME};
done

# Symlink to persistent storage
# rm -rf ${2}/public/uploads;
# ln -s ${3}/storage/public/uploads ${2}/public/uploads;

# Symlink everything in storage/public
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

# Symlink Env-Specific Files
ln -s ${3}/storage/.env ${2}/.env;

# Update asset versioning
timestamp=$(date +%s);
cp ${2}/public/assets/css/style.min.css ${2}/public/assets/css/style.min.${timestamp}.css;
cp ${2}/public/assets/js/script.min.js ${2}/public/assets/js/script.min.${timestamp}.js;
sed -i -e "s/'staticAssetCacheTime' => ''/'staticAssetCacheTime' => $timestamp/g" ${2}/config/general.php;

# Update file permissions
sudo chmod -R 0777 ${2}/public/cache;
sudo chmod -R 0777 ${2}/public/cpresources;

# Fix a cache issue that prevents Envoyer from deleting old releases
for f in ${3}/releases/*; do
    if [ -d "${f}/public/cache" ]; then
        sudo chmod -R 0777 ${f}/public/cache;
    fi
    if [ -d "${f}/public/cpresources" ]; then
        sudo chmod -R 0777 ${f}/public/cpresources;
    fi
done;
