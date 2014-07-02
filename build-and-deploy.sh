#!/bin/bash

pushd `dirname $0`

echo
echo "This task is about building and deploying cushyerp software to production."
echo "WARNING: if you give the correct password to this script, it WILL update  "
echo "and modify the production code. "
echo 
echo "If you give incorrect password, it will still create the build but the"
echo "passwords in the created archive will be incorrect and the script "
echo "will be unable to do any actual changes to the production due to the "
echo "necessary ssh connection failing."

ANT_OPTIONS="-Dproject.target.build=local -Doptions.app.deploy=true"

echo
echo -n "The remote target to deploy to [amazon1/testing1/unstable1/prod3/prod4/aprod1]? "
read INPUT_TARGET

if [ x${INPUT_TARGET} == xprod3 ]; then
    echo
    echo "Ok, will be building and deploying to martela/grundell OLD production environment."

    echo
    echo -n "The email password to use for linux user cushyerp? "
    read -s INPUT_PASSWORD

    if [ x${INPUT_PASSWORD} == x ]; then
        echo
        echo "No password given. Exiting with no changes being made."
        echo
        exit -12
    fi

    ANT_OPTIONS="${ANT_OPTIONS} -Dproject.target.deploy=${INPUT_TARGET} -D${INPUT_TARGET}.mail.password=${INPUT_PASSWORD}"


elif [ x${INPUT_TARGET} == xprod4 ]; then
    echo
    echo "Ok, will be building and deploying to martela/grundell production environment prod4."

    echo
    echo -n "The email password to use for linux user cushyerp? "
    read -s INPUT_PASSWORD

    if [ x${INPUT_PASSWORD} == x ]; then
        echo
        echo "No password given. Exiting with no changes being made."
        echo
        exit -12
    fi

    echo
    echo -n "The hostname/IP for the server to update? "
    read INPUT_HOSTNAME

    if [ x${INPUT_HOSTNAME} == x ]; then
        echo
        echo "No hostname/IP given. Exiting with no changes being made."
        echo
        exit -12
    fi

    ANT_OPTIONS="${ANT_OPTIONS} -Dproject.target.deploy=${INPUT_TARGET} -D${INPUT_TARGET}.mail.password=${INPUT_PASSWORD}"

elif [ x${INPUT_TARGET} == xamazon1 ]; then
    echo
    echo "Ok, will be building and deploying to amazon development environment."

    echo
    echo -n "The hostname/IP for the amazon EC2 server to update? "
    read INPUT_HOSTNAME

    if [ x${INPUT_HOSTNAME} == x ]; then
        echo
        echo "No hostname/IP given. Exiting with no changes being made."
        echo
        exit -12
    fi

    ANT_OPTIONS="${ANT_OPTIONS} -Dproject.target.deploy=${INPUT_TARGET} -D${INPUT_TARGET}.shell.host=${INPUT_HOSTNAME}"

elif [ x${INPUT_TARGET} == xtesting1 ]; then
    echo
    echo "Ok, will be building and deploying to amazon testing environment."

    echo
    echo -n "The hostname/IP for the amazon EC2 server to update? "
    read INPUT_HOSTNAME

    if [ x${INPUT_HOSTNAME} == x ]; then
        echo
        echo "No hostname/IP given. Exiting with no changes being made."
        echo
        exit -12
    fi

    ANT_OPTIONS="${ANT_OPTIONS} -Dproject.target.deploy=${INPUT_TARGET} -D${INPUT_TARGET}.shell.host=${INPUT_HOSTNAME}"

elif [ x${INPUT_TARGET} == xunstable1 ]; then
    echo
    echo "Ok, will be building and deploying to amazon unstable environment."

    echo
    echo -n "The hostname/IP for the amazon EC2 server to update? "
    read INPUT_HOSTNAME

    if [ x${INPUT_HOSTNAME} == x ]; then
        echo
        echo "No hostname/IP given. Exiting with no changes being made."
        echo
        exit -12
    fi

    ANT_OPTIONS="${ANT_OPTIONS} -Dproject.target.deploy=${INPUT_TARGET} -D${INPUT_TARGET}.shell.host=${INPUT_HOSTNAME}"

elif [ x${INPUT_TARGET} == xaprod1 ]; then
    echo
    echo "Ok, will be building and deploying to amazon unstable environment."

    echo
    echo -n "The hostname/IP for the amazon EC2 server to update? "
    read INPUT_HOSTNAME

    if [ x${INPUT_HOSTNAME} == x ]; then
        echo
        echo "No hostname/IP given. Exiting with no changes being made."
        echo
        exit -12
    fi

    ANT_OPTIONS="${ANT_OPTIONS} -Dproject.target.deploy=${INPUT_TARGET} -D${INPUT_TARGET}.shell.host=${INPUT_HOSTNAME}"

else
    echo
    echo "Unknown target ${INPUT_TARGET}, exiting with no changes being made."
    echo
    exit -11

fi

echo
echo -n "The keyfile to use to access the remote server? "
read INPUT_KEYFILE

if [ x${INPUT_KEYFILE} == x ]; then
    echo
    echo "No keyfile. Exiting with no changes being made."
    echo
    exit -12
fi

if [ -e ${INPUT_KEYFILE} ]; then
    echo Keyfile ${INPUT_KEYFILE} found.
else
    echo Keyfile ${INPUT_KEYFILE} not found.
    exit -13;
fi

ANT_OPTIONS="${ANT_OPTIONS} -D${INPUT_TARGET}.shell.keyfile=${INPUT_KEYFILE}"

echo
echo -n "Ok, about to start the build. Are you sure to go forward [y/n]? "
read  INPUT_CONFIRM

if [ x${INPUT_CONFIRM} == xy ]; then
    echo
    echo "Ok ... hope you know what you are doing ..."
else
    echo
    echo "Ok, exiting with no changes being made."
    echo
    exit -10
fi

echo
echo "Starting the build for target ${INPUT_TARGET}"

echo
ant :dist :deploy ${ANT_OPTIONS}

echo
echo "Please note that this script does NOT run the database upgrade migrations, at the moment you have to that by yourself."
echo
echo "Done."
echo

popd
