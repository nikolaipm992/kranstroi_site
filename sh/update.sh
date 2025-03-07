#!bin/bash

# PHPShop Updater 

RED='\033[01;31m'
GREEN='\033[01;32m'
YELLOW='\033[01;33m'
CYAN='\033[01;36m'
WHITE='\033[01;37m'
NC='\033[00m'
TMP='../backup/temp/'
V='1.1'

if [ $# = 0 ] ; then 
    /bin/echo -e  "${CYAN}PHPShop Updater ${V}${NC}"
fi

# check unzip lib
if [ ! $(which unzip) ] ; then 
   /bin/echo -e "${RED}You need to install the unzip library!${NC}"
   exit 0
fi


# license
. `pwd`/../license/`ls ../license`  > /dev/null 2>&1

if [ $(echo $DomenLocked | tr -d '\r' ) = "" ] ; then 
   /bin/echo -e  "${RED}Failed to find the license file, run this file in /sh/ directory!${NC}"
   exit 0
fi

# get link
link=`php cli.lib.php link`

if [ $1 ] ; then 
   ready="y"
fi

# check new version
wget "$link&check=true" -O ${TMP}l q >/dev/null 2>&1
version=`more ${TMP}l`

if [ $version = "no_update" ] ; then 
   /bin/echo -e  "${YELLOW}You have the latest version, no updates required${NC}"
   exit 0
elif [ $version = "passive" ] ; then 
   /bin/echo -e "${YELLOW}To update, you need to extend the technical support!${NC}"
   exit 0
elif [ $# = 0 ] ; then
   /bin/echo -e "${NC}New PHPShop ${version} is available, update now? (y / n)${NC}"
   read ready
   if [ $ready = "n" ] ; then 
   exit 0
   fi
fi

# get current
current=`php cli.lib.php version`

# zip http://www.softpanorama.org/Utilities/unzip.shtml
wget "$link&file=zip" -O ${TMP}update.zip -q >/dev/null 2>&1

# ini
wget "$link&file=ini" -O ${TMP}config_update.txt -q >/dev/null 2>&1

if [ -s ${TMP}update.zip ] ; then 
   /bin/echo -e "${GREEN}Loading  is finished...${NC}"
else /bin/echo -e "${RED}Downloading update files failed!${NC}"
   exit 0;
fi

unzip -o ${TMP}update.zip -d ../
rm -rf ${TMP}update.zip
/bin/echo -e "${GREEN}Unpacking is finished...${NC}"

# update mysql
wget "$link&file=sql" -O ${TMP}update.sql -q >/dev/null 2>&1

if [ -s ${TMP}update.sql ] ; then
   result=`php cli.lib.php sql`

   if [ $result = "done" ] ; then 
   /bin/echo -e "${GREEN}Update MySQL is finished...${NC}"
   rm -rf ${TMP}update.sql
   fi
fi

# update ini
chmod 775 ../phpshop/inc/config.ini
result=`php cli.lib.php ini`

if [ $result = "done" ] ; then 
   /bin/echo -e "${GREEN}Update config is finished...${NC}"
   rm -rf ${TMP}config_update.txt
else 
   /bin/echo -e "${RED}Configuration file can not be updated!${NC}"
   exit 0
fi

#backup
wget "$link&file=backup" -O ${TMP}upd_conf.txt -q >/dev/null 2>&1
wget "$link&file=restore" -O ${TMP}restore.sql -q >/dev/null 2>&1
mkdir ../backup/backups/$current >/dev/null 2>&1
chmod 775 ../backup/backups/$current >/dev/null 2>&1

result=`php cli.lib.php backup $current`
if [ $result = "done" ] ; then 
   /bin/echo -e "${GREEN}Backup version ${current} is finished...${NC}"
   rm -rf ${TMP}upd_conf.txt
   rm -rf ${TMP}restore.sql
else 
   /bin/echo -e "${RED}Backup version ${current} can not be created!${NC}"
fi


/bin/echo -e "${CYAN}Your PHPShop update successful to version ${version} ${NC}"


# check new version one more
wget "$link&check=true" -O ${TMP}l -q >/dev/null 2>&1
version=`more ${TMP}l`
rm -rf ${TMP}l

if [ $version = "no_update" ] ; then 
   /bin/echo -e "${CYAN}No more updates, you have the latest version${NC}"
   else  
   /bin/echo -e "${NC}New PHPShop ${version} is available, update now? (y / n)${NC}"
   read ready

   if [ $ready = "y" ] ; then 
   sh $0 $version
   exit 0
   fi

fi