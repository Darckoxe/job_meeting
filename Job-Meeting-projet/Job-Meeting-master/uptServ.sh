#/bin/bash

if [ $#=1 ] ; then
    intervale=$1
    else
        intervale=10
fi

echo "MAJ du serveur toutes les n min"
while [ $? ] ; do
    git pull & sleep $intervale
done
