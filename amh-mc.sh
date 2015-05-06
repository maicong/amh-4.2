#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH

clear;
echo '================================================================';
echo ' [LNMP/Nginx] Amysql Host - AMH 4.2 - Modified By MaiCong ';
echo ' http://Amysql.com & https://github.com/maicong/amh-4.2 ';
echo '================================================================';


# VAR ***************************************************************************************
AMHDir='/home/amh_install';
SysName='';
SysBit='';
Cpunum='';
RamTotal='';
RamSwap='';
RamSum='';
InstallModel='';
Domain=`ip addr | egrep -o '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' | egrep -v "^192\.168|^172\.1[6-9]\.|^172\.2[0-9]\.|^172\.3[0-2]\.|^10\.|^127\.|^255\." | head -n 1`;
MysqlPass='';
AMHPass='';
StartDate='';
StartDateSecond='';
PHPDisable='';

# GetUrl
GetUrl='';

# Version
AMSVersion='ams-1.5.0107-02';
AMHVersion='amh-4.2';
AMHConfVersion='amh-4.2-conf';
LibiconvVersion='libiconv-1.14';
LibunwindVersion='libunwind-1.1';
GperftoolsVersion='gperftools-2.4';
PcreVersion='pcre-8.36';
ZlibVersion='zlib-1.2.8';
OpensslVersion='openssl-1.0.2a';
NgxcachepurgeVersion='ngx_cache_purge-2.3';
NgxpagespeedVersion='ngx_pagespeed-1.9.32.3';
LibmcryptVersion='libmcrypt-2.5.8';
MhashVersion='mhash-0.9.9.9';
MCryptVersion='mcrypt-2.6.8';
MysqlVersion='mysql-5.6.24';
PhpVersion='php-5.6.8';
NginxVersion='tengine-2.1.0';
PureFTPdVersion='pure-ftpd-1.0.36';
NeusshblFiLe='/usr/local/bin/fetch_neusshbl.sh';
NeusshblLink='/etc/cron.hourly/fetch_neusshbl.sh';

# Function List *****************************************************************************
function CheckSystem()
{
    [ $(id -u) != '0' ] && echo '[Error] Please use root to install AMH.' && exit;
    egrep -i "debian" /etc/issue /proc/version >/dev/null && SysName='Debian';
    egrep -i "ubuntu" /etc/issue /proc/version >/dev/null && SysName='Ubuntu';
    whereis -b yum | grep '/yum' >/dev/null && SysName='CentOS';
    [ "$SysName" == ''  ] && echo '[Error] Your system is not supported install AMH' && exit;

    SysBit='32' && [ `getconf WORD_BIT` == '32' ] && [ `getconf LONG_BIT` == '64' ] && SysBit='64';
    Cpunum=`cat /proc/cpuinfo | grep 'processor' | wc -l`;
    echo "${SysName} ${SysBit}Bit";
    RamTotal=`free -m | grep 'Mem' | awk '{print $2}'`;
    RamSwap=`free -m | grep 'Swap' | awk '{print $2}'`;
    echo "Server ${IPAddress}";
    echo "${Cpunum}*CPU, ${RamTotal}MB*RAM, ${RamSwap}MB*Swap";
    echo '================================================================';
    
    RamSum=$[$RamTotal+$RamSwap];
    [ "$SysBit" == '32' ] && [ "$RamSum" -lt '250' ] && \
    echo -e "[Error] Not enough memory install AMH. \n(32bit system need memory: ${RamTotal}MB*RAM + ${RamSwap}MB*Swap > 250MB)" && exit;

    if [ "$SysBit" == '64' ] && [ "$RamSum" -lt '480' ];  then
        echo -e "[Error] Not enough memory install AMH. \n(64bit system need memory: ${RamTotal}MB*RAM + ${RamSwap}MB*Swap > 480MB)";
        [ "$RamSum" -gt '250' ] && echo "[Notice] Please use 32bit system.";
        exit;
    fi;
}

function ConfirmInstall()
{
    echo "[Notice] Confirm Install/Uninstall AMH? please select: (1~3)"
    select selected in 'Install AMH 4.2' 'Uninstall AMH 4.2' 'Exit'; do break; done;
    [ "$selected" == 'Exit' ] && echo 'Exit Install.' && exit;
        
    if [ "$selected" == 'Install AMH 4.2' ]; then
        InstallModel='1';
    elif [ "$selected" == 'Uninstall AMH 4.2' ]; then
        Uninstall;
    else
        ConfirmInstall;
        return;
    fi;

    echo "[OK] You Selected: ${selected}";
}

function SelectLocation()
{
    echo -e "[Notice] Please select your nearest mirror: (1~5)";
    select ServerLocation in 'coding.net [CN-Git]' 'raw.githubusercontent.com [USA-Git]' 'xcdn.yuxiaoxi.com [CN-CDN]' 'cdn.rawgit.com [USA-CDN]' 'Exit'; do break; done;

    [ "$ServerLocation" == 'Exit' ] && echo 'Exit Install.' && exit;
    [ "$ServerLocation" != '' ] &&  echo -e "[OK] Your Location: ${ServerLocation}\n";

    if [ "$ServerLocation" == 'coding.net [CN-Git]' ]; then
        GetUrl='https://coding.net/u/maicong/p/AMH-4.2/git/raw/master';
    elif [ "$ServerLocation" == 'raw.githubusercontent.com [USA-Git]' ]; then
        GetUrl='https://raw.githubusercontent.com/maicong/amh-4.2/master';
    elif [ "$ServerLocation" == 'xcdn.yuxiaoxi.com [CN-CDN]' ]; then
        GetUrl='http://xcdn.yuxiaoxi.com/amh-4.2';
    elif [ "$ServerLocation" == 'cdn.rawgit.com [USA-CDN]' ]; then
        GetUrl='https://cdn.rawgit.com/maicong/amh-4.2/master';
    else
        SelectLocation;
        return;
    fi; 
}

function InputDomain()
{
    if [ "$Domain" == '' ]; then
        echo '[Error] empty server ip.';
        read -p '[Notice] Please input server ip:' Domain;
        [ "$Domain" == '' ] && InputDomain;
    fi;
    [ "$Domain" != '' ] && echo '[OK] Your server ip is:' && echo $Domain;
}


function InputMysqlPass()
{
    if [ "$MysqlPass" == '' ]; then
        read -p '[Notice] Please input MySQL password:' MysqlPass;
        echo '[Error] MySQL password is empty.';
        InputMysqlPass;
    else
        echo '[OK] Your MySQL password is:';
        echo $MysqlPass;
    fi;
}


function InputAMHPass()
{
    if [ "$AMHPass" == '' ]; then
        read -p '[Notice] Please input AMH password:' AMHPass;
        echo '[Error] AMH password empty.';
        InputAMHPass;
    else
        echo '[OK] Your AMH password is:';
        echo $AMHPass;
    fi;
}


function Timezone()
{
    rm -rf /etc/localtime;
    ln -s /usr/share/zoneinfo/Asia/Shanghai /etc/localtime;

    echo '[ntp Installing] ******************************** >>';
    [ "$SysName" == 'CentOS' ] && yum install -y ntp || apt-get install -y ntpdate;
    ntpdate -u pool.ntp.org;
    StartDate=$(date);
    StartDateSecond=$(date +%s);
    echo "Start time: ${StartDate}";
}


function CloseSelinux()
{
    [ -s /etc/selinux/config ] && sed -i 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config;
    setenforce 0 >/dev/null 2>&1;
}

function DeletePackages()
{
    if [ "$SysName" == 'CentOS' ]; then
        yum -y remove httpd;
        yum -y remove php;
        yum -y remove mysql-server mysql;
        yum -y remove php-mysql;
    else
        apt-get --purge remove nginx
        apt-get --purge remove mysql-server;
        apt-get --purge remove mysql-common;
        apt-get --purge remove php;
    fi;
}

function InstallBasePackages()
{
    if [ "$SysName" == 'CentOS' ]; then
        echo '[yum-fastestmirror Installing] ************************************************** >>';
        yum -y install yum-fastestmirror;

        cp /etc/yum.conf /etc/yum.conf.lnmp
        sed -i 's:exclude=.*:exclude=:g' /etc/yum.conf
        for packages in gcc gcc-c++ ncurses-devel libxml2-devel openssl-devel curl-devel libjpeg-devel libpng-devel autoconf pcre-devel libtool-libs freetype-devel gd zlib zlib-devel zip unzip wget crontabs iptables file bison cmake patch mlocate flex diffutils automake make  readline-devel git glibc-devel glibc-static glib2-devel  bzip2-devel gettext-devel libcap-devel logrotate ftp openssl expect; do 
            echo "[${packages} Installing] ************************************************** >>";
            yum -y install $packages; 
        done;
        mv -f /etc/yum.conf.lnmp /etc/yum.conf;
    else
        apt-get remove -y apache2 apache2-doc apache2-utils apache2.2-common apache2.2-bin apache2-mpm-prefork apache2-doc apache2-mpm-worker mysql-client mysql-server mysql-common php;
        killall apache2;
        apt-get update;
        for packages in build-essential gcc g++ git git-core cmake make ntp logrotate automake patch autoconf autoconf2.13 re2c wget flex cron libzip-dev libc6-dev rcconf bison cpp binutils unzip tar bzip2 libncurses5-dev libncurses5 libtool libevent-dev libpcre3 libpcre3-dev libpcrecpp0 libssl-dev zlibc openssl libsasl2-dev libxml2 libxml2-dev libltdl3-dev libltdl-dev zlib1g zlib1g-dev libbz2-1.0 libbz2-dev libglib2.0-0 libglib2.0-dev libpng3 libfreetype6 libfreetype6-dev libjpeg62 libjpeg62-dev libjpeg-dev libpng-dev libpng12-0 libpng12-dev curl libcurl3 libpq-dev libpq5 gettext libcurl4-gnutls-dev  libcurl4-openssl-dev libcap-dev ftp openssl expect; do
            echo "[${packages} Installing] ************************************************** >>";
            apt-get install -y $packages --force-yes;apt-get -fy install;apt-get -y autoremove; 
        done;
    fi;
}


function Downloadfile()
{
    randstr=$(date +%s);
    cd $AMHDir/packages;

    if [ -s $1 ]; then
        echo "[OK] $1 found.";
    else
        echo "[Notice] $1 not found, download now......";
        if ! wget -c --tries=3 ${2}?${randstr} ; then
            echo "[Error] Download Failed : $1, please check $2 ";
            exit;
        else
            mv ${1}?${randstr} $1;
        fi;
    fi;
}

function InstallReady()
{
    mkdir -p $AMHDir/conf;
    mkdir -p $AMHDir/packages/untar;
    chmod +rw $AMHDir/packages;

    mkdir -p /root/amh/;
    chmod +rw /root/amh;

    groupadd www;
    useradd www -g www -M -s /sbin/nologin;

    randstr=$(date +%s);

    cd $AMHDir/packages;
    wget ${GetUrl}/${AMHConfVersion}.zip;
    unzip ${AMHConfVersion}.zip -d $AMHDir/conf;
}


# Install Function  *********************************************************

function Uninstall()
{
    amh host list 2>/dev/null;
    echo -e "\033[41m\033[37m[Warning] Please backup your data first. Uninstall will delete all the data!!! \033[0m ";
    read -p '[Notice] Backup the data now? : (y/n)' confirmBD;
    [ "$confirmBD" != 'y' -a "$confirmBD" != 'n' ] && exit;
    [ "$confirmBD" == 'y' ] && amh backup;
    echo '=============================================================';

    read -p '[Notice] Confirm Uninstall(Delete All Data)? : (y/n)' confirmUN;
    [ "$confirmUN" != 'y' ] && exit;
    amh mysql stop 2>/dev/null;
    amh php stop 2>/dev/null;
    amh nginx stop 2>/dev/null;

    killall nginx;
    killall mysqld;
    killall pure-ftpd;
    killall php-cgi;
    killall php-fpm;

    userdel www;
    userdel mysql;
    userdel ftpuser;

    [ "$SysName" == 'CentOS' ] && chkconfig amh-start off || update-rc.d -f amh-start remove;
    rm -rf /etc/init.d/amh-start;
    rm -rf /usr/local/libiconv;
    rm -rf /usr/local/libunwind;
    rm -rf /usr/local/gperftools;
    rm -rf /usr/local/nginx/;
    rm -rf /usr/local/pcre;
    rm -rf /usr/local/zlib;
    rm -rf /usr/local/openssl;
    rm -rf /usr/local/src/$PcreVersion;
    rm -rf /usr/local/src/$ZlibVersion;
    rm -rf /usr/local/src/$OpensslVersion;
    rm -rf /usr/local/src/$NgxcachepurgeVersion;
    rm -rf /usr/local/src/$NgxpagespeedVersion;
    rm -rf /usr/local/src/ngx_http_substitutions_filter_module;
    for line in `ls /root/amh/modules`; do
        amh module $line uninstall;
    done;
    rm -rf /usr/local/mysql/ /etc/my.cnf  /etc/ld.so.conf.d/mysql.conf /etc/ld.so.conf.d/local.conf /usr/bin/mysql /var/lock/subsys/mysql /var/spool/mail/mysql;
    rm -rf /usr/local/php/ /usr/lib/php /etc/php.ini /etc/php.d /usr/local/zend;
    rm -rf /home/wwwroot/;
    rm -rf /home/mysql_data/;
    rm -rf /etc/pure-ftpd.conf /etc/pam.d/ftp /usr/local/sbin/pure-ftpd /etc/pureftpd.passwd /etc/amh-iptables;
    rm -rf /etc/logrotate.d/nginx /root/.mysqlroot /root/.mysql_history;
    rm -rf /root/amh /bin/amh;
    rm -rf $AMHDir;
    rm -f /usr/bin/{mysqld_safe,myisamchk,mysqldump,mysqladmin,mysql,nginx,php-fpm,phpize,php};

    for f in $(find $1 -type l); do [ -e $f ] && rm -f $f; done

    echo '[OK] Successfully uninstall AMH.';
    exit;
}

function InstallLibiconv()
{
    # [dir] /usr/local/libiconv
    echo "[${LibiconvVersion} Installing] ************************************************** >>";
    if [ ! -d /usr/local/libiconv ]; then
        Downloadfile "${LibiconvVersion}.tar.gz" "${GetUrl}/${LibiconvVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$LibiconvVersion;
        echo "tar -zxf ${LibiconvVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$LibiconvVersion.tar.gz -C $AMHDir/packages/untar;

        cd $AMHDir/packages/untar/$LibiconvVersion;
        sed -i 's@_GL_WARN_ON_USE (gets@//_GL_WARN_ON_USE (gets@' ./srclib/stdio.in.h;
        sed -i 's@gets is a security@@' ./srclib/stdio.in.h;
        ./configure --prefix=/usr/local/libiconv;
        make;
        make install;
        echo "[OK] ${LibiconvVersion} install completed.";
    else
        echo '[OK] libiconv is installed!';
    fi;
}

function InstallGperftools(){
    # [dir] /usr/local/gperftools
    echo "[Gperftools Installing] ************************************************** >>";
    if [ ! -f /usr/local/gperftools ]; then
        Downloadfile "${GperftoolsVersion}.tar.gz" "${GetUrl}/${GperftoolsVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$GperftoolsVersion;
        echo "tar -zxf ${GperftoolsVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$GperftoolsVersion.tar.gz -C $AMHDir/packages/untar;

        if [ "$SysBit" == '64' ] ; then
            if [ ! -f /usr/local/libunwind ]; then
                Downloadfile "${LibunwindVersion}.tar.gz" "${GetUrl}/${LibunwindVersion}.tar.gz";
                rm -rf $AMHDir/packages/untar/$LibunwindVersion;
                echo "tar -zxf ${LibunwindVersion}.tar.gz ing...";
                tar -zxf $AMHDir/packages/$LibunwindVersion.tar.gz -C $AMHDir/packages/untar;

                cd $AMHDir/packages/untar/$LibunwindVersion;
                CFLAGS=-fPIC ./configure --prefix=/usr/local/libunwind;
                make CFLAGS=-fPIC;
                make CFLAGS=-fPIC install;
            fi;
            cd $AMHDir/packages/untar/$GperftoolsVersion;
            LDFLAGS="-L/usr/local/libunwind/lib" CPPFLAGS="-I/usr/local/libunwind/include" ./configure --prefix=/usr/local/gperftools
        else
            cd $AMHDir/packages/untar/$GperftoolsVersion;
            ./configure --prefix=/usr/local/gperftools --enable-frame-pointers 
        fi;

        make -j $Cpunum;
        make install;
            echo "/usr/local/lib" >> /etc/ld.so.conf.d/local.conf;
        if [ "$SysBit" == '64' ] ; then
            echo "/usr/local/lib64" >> /etc/ld.so.conf.d/local.conf;
        fi; 
        echo "/usr/local/libunwind/lib" >> /etc/ld.so.conf.d/local.conf;
        echo "/usr/local/gperftools/lib" >> /etc/ld.so.conf.d/local.conf;
        ldconfig -v;

        mkdir -p /tmp/tcmalloc;
        chmod 0777 /tmp/tcmalloc;

        echo "[OK] Gperftools install completed.";
    else
        echo '[OK] Gperftools is installed!';
    fi;
}

function InstallPcre()
{
    # [dir] /usr/local/pcre /usr/local/src/pcre*
    echo "[${PcreVersion} Installing] ************************************************** >>";
    if [ ! -d /usr/local/pcre ]; then
        Downloadfile "${PcreVersion}.tar.gz" "${GetUrl}/${PcreVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$PcreVersion;
        echo "tar -zxf ${PcreVersion}.tar.gz ...";
        tar -zxf $AMHDir/packages/$PcreVersion.tar.gz -C /usr/local/src;
    
        cd /usr/local/src/$PcreVersion;
        ./configure --prefix=/usr/local/pcre --enable-utf8 --enable-unicode-properties;
        make;
        make install;
        echo '/usr/local/pcre/lib' >> /etc/ld.so.conf.d/local.conf;
        ldconfig -v;
        echo "[OK] ${PcreVersion} install completed.";
    else
        echo '[OK] pcre is installed!';
    fi;
}

function InstallZlib()
{
    # [dir] /usr/local/zlib /usr/local/src/zlib*
    echo "[${ZlibVersion} Installing] ************************************************** >>";
    if [ ! -d /usr/local/zlib ]; then
        Downloadfile "${ZlibVersion}.tar.gz" "${GetUrl}/${ZlibVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$ZlibVersion;
        echo "tar -zxf ${ZlibVersion}.tar.gz ...";
        tar -zxf $AMHDir/packages/$ZlibVersion.tar.gz -C /usr/local/src;
    
        cd /usr/local/src/$ZlibVersion;
        ./configure --prefix=/usr/local/zlib;
        make;
        make install;
        echo '/usr/local/zlib/lib' >> /etc/ld.so.conf.d/local.conf;
        ldconfig -v;
        echo "[OK] ${ZlibVersion} install completed.";
    else
        echo '[OK] zlib is installed!';
    fi;
}

function InstallOpenssl()
{
    # [dir] /usr/local/openssl /usr/local/src/openssl*
    echo "[${OpensslVersion} Installing] ************************************************** >>";
    if [ ! -d /usr/local/openssl ]; then
        Downloadfile "${OpensslVersion}.tar.gz" "${GetUrl}/${OpensslVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$OpensslVersion;
        echo "tar -zxf ${OpensslVersion}.tar.gz ...";
        tar -zxf $AMHDir/packages/$OpensslVersion.tar.gz -C /usr/local/src;

        cd /usr/local/src/$OpensslVersion;
        ./config --prefix=/usr/local/openssl;
        make;
        make install;
        echo "[OK] ${OpensslVersion} install completed.";
    else
        echo '[OK] openssl is installed!';
    fi;
}

function InstallMysql()
{
    # [dir] /usr/local/mysql/
    echo "[${MysqlVersion} Installing] ************************************************** >>";
    if [ ! -f /usr/local/mysql/bin/mysql ]; then
        Downloadfile "${MysqlVersion}.tar.gz" "${GetUrl}/${MysqlVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$MysqlVersion;
        echo "tar -zxf ${MysqlVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$MysqlVersion.tar.gz -C $AMHDir/packages/untar;

        cd $AMHDir/packages/untar/$MysqlVersion;
        cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql -DMYSQL_DATADIR=/home/mysql_data -DSYSCONFDIR=/etc -DWITH_MYISAM_STORAGE_ENGINE=1 -DWITH_INNOBASE_STORAGE_ENGINE=1 -DWITH_MEMORY_STORAGE_ENGINE=1 -DWITH_PARTITION_STORAGE_ENGINE=1 -DWITH_FEDERATED_STORAGE_ENGINE=1 -DWITH_BLACKHOLE_STORAGE_ENGINE=1 -DWITH_READLINE=1 -DENABLED_LOCAL_INFILE=1 -DENABLE_DTRACE=0 -DEXTRA_CHARSETS=all -DDEFAULT_CHARSET=utf8 -DDEFAULT_COLLATION=utf8_general_ci -DWITH_EXTRA_CHARSETS=complex;
        make -j $Cpunum;
        make install;   

        groupadd mysql;
        useradd mysql -g mysql -M -s /sbin/nologin;
        chmod +w /usr/local/mysql;
        chown -R mysql:mysql /usr/local/mysql;
        mkdir -p /home/mysql_data;
        chown -R mysql:mysql /home/mysql_data;

        rm -f /etc/mysql/my.cnf /usr/local/mysql/etc/my.cnf;
        cp $AMHDir/conf/my.cnf /etc/my.cnf;
        cp $AMHDir/conf/mysql /root/amh/mysql;
        chmod +x /root/amh/mysql;

        if [ $RamTotal -gt 1500 -a $RamTotal -le 2500 ];then
                sed -i 's@^thread_cache_size.*@thread_cache_size = 16@' /etc/my.cnf;
                sed -i 's@^query_cache_size.*@query_cache_size = 16M@' /etc/my.cnf;
                sed -i 's@^myisam_sort_buffer_size.*@myisam_sort_buffer_size = 16M@' /etc/my.cnf;
                sed -i 's@^key_buffer_size.*@key_buffer_size = 16M@' /etc/my.cnf;
                sed -i 's@^innodb_buffer_pool_size.*@innodb_buffer_pool_size = 128M@' /etc/my.cnf;
                sed -i 's@^tmp_table_size.*@tmp_table_size = 32M@' /etc/my.cnf;
                sed -i 's@^table_open_cache.*@table_open_cache = 256@' /etc/my.cnf;
        elif [ $RamTotal -gt 2500 -a $RamTotal -le 3500 ];then
                sed -i 's@^thread_cache_size.*@thread_cache_size = 32@' /etc/my.cnf;
                sed -i 's@^query_cache_size.*@query_cache_size = 32M@' /etc/my.cnf;
                sed -i 's@^myisam_sort_buffer_size.*@myisam_sort_buffer_size = 32M@' /etc/my.cnf;
                sed -i 's@^key_buffer_size.*@key_buffer_size = 64M@' /etc/my.cnf;
                sed -i 's@^innodb_buffer_pool_size.*@innodb_buffer_pool_size = 512M@' /etc/my.cnf;
                sed -i 's@^tmp_table_size.*@tmp_table_size = 64M@' /etc/my.cnf;
                sed -i 's@^table_open_cache.*@table_open_cache = 512@' /etc/my.cnf;
        elif [ $RamTotal -gt 3500 ];then
                sed -i 's@^thread_cache_size.*@thread_cache_size = 64@' /etc/my.cnf;
                sed -i 's@^query_cache_size.*@query_cache_size = 64M@' /etc/my.cnf;
                sed -i 's@^myisam_sort_buffer_size.*@myisam_sort_buffer_size = 64M@' /etc/my.cnf;
                sed -i 's@^key_buffer_size.*@key_buffer_size = 256M@' /etc/my.cnf;
                sed -i 's@^innodb_buffer_pool_size.*@innodb_buffer_pool_size = 1024M@' /etc/my.cnf;
                sed -i 's@^tmp_table_size.*@tmp_table_size = 128M@' /etc/my.cnf;
                sed -i 's@^table_open_cache.*@table_open_cache = 1024@' /etc/my.cnf;
        fi;
        sed -i 's@executing mysqld_safe@executing mysqld_safe\nexport LD_PRELOAD=/usr/local/gperftools/lib/libtcmalloc.so@' /usr/local/mysql/bin/mysqld_safe
        /usr/local/mysql/scripts/mysql_install_db --user=mysql --defaults-file=/etc/my.cnf --basedir=/usr/local/mysql --datadir=/home/mysql_data;
        

# EOF **********************************
cat > /etc/ld.so.conf.d/mysql.conf<<EOF
/usr/local/mysql/lib/mysql
EOF
# **************************************

        ldconfig -v;
        if [ "$SysBit" == '64' ] ; then
            ln -s /usr/local/mysql/lib/mysql /usr/lib64/mysql;
        else
            ln -s /usr/local/mysql/lib/mysql /usr/lib/mysql;
        fi;
        chmod 775 /usr/local/mysql/support-files/mysql.server;
        /usr/local/mysql/support-files/mysql.server start;
        ln -s /usr/local/mysql/bin/mysql /usr/bin/mysql;
        ln -s /usr/local/mysql/bin/mysqladmin /usr/bin/mysqladmin;
        ln -s /usr/local/mysql/bin/mysqldump /usr/bin/mysqldump;
        ln -s /usr/local/mysql/bin/myisamchk /usr/bin/myisamchk;
        ln -s /usr/local/mysql/bin/mysqld_safe /usr/bin/mysqld_safe;

        /usr/local/mysql/bin/mysqladmin password $MysqlPass;
        rm -rf /home/mysql_data/test;

# EOF **********************************
mysql -hlocalhost -uroot -p$MysqlPass <<EOF
USE mysql;
DELETE FROM user WHERE User!='root' OR (User = 'root' AND Host != 'localhost');
UPDATE user set password=password('$MysqlPass') WHERE User='root';
DROP USER ''@'%';
FLUSH PRIVILEGES;
EOF
# **************************************
        echo "[OK] ${MysqlVersion} install completed.";
    else
        echo '[OK] MySQL is installed.';
    fi;
}

function InstallPhp()
{
    # [dir] /usr/local/php
    echo "[${PhpVersion} Installing] ************************************************** >>";
    if [ ! -d /usr/local/php ]; then
        Downloadfile "${LibmcryptVersion}.tar.gz" "${GetUrl}/${LibmcryptVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$LibmcryptVersion;
        echo "tar -zxf ${LibmcryptVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$LibmcryptVersion.tar.gz -C $AMHDir/packages/untar;

        cd $AMHDir/packages/untar/$LibmcryptVersion;
        ./configure
        make && make install;
        ldconfig -v;
        cd libltdl/
        ./configure --enable-ltdl-install
        make && make install;

        Downloadfile "${MhashVersion}.tar.gz" "${GetUrl}/${MhashVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$MhashVersion;
        echo "tar -zxf ${MhashVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$MhashVersion.tar.gz -C $AMHDir/packages/untar;

        cd $AMHDir/packages/untar/$MhashVersion;
        ./configure
        make && make install;

        if [ "$SysName" == 'CentOS' ]; then
            ln -s /usr/local/bin/libmcrypt-config /usr/bin/libmcrypt-config;
            if [ `getconf WORD_BIT` == 32 ] && [ `getconf LONG_BIT` == 64 ]; then
                ln -s /lib64/libpcre.so.0.0.1 /lib64/libpcre.so.1;
            else
                ln -s /lib/libpcre.so.0.0.1 /lib/libpcre.so.1;
            fi;
        fi;

        Downloadfile "${MCryptVersion}.tar.gz" "${GetUrl}/${MCryptVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$MCryptVersion;
        echo "tar -zxf ${MCryptVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$MCryptVersion.tar.gz -C $AMHDir/packages/untar;

        cd $AMHDir/packages/untar/$MCryptVersion;
        ./configure
        make && make install;

        Downloadfile "${PhpVersion}.tar.gz" "${GetUrl}/${PhpVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$PhpVersion;
        echo "tar -zxf ${PhpVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$PhpVersion.tar.gz -C $AMHDir/packages/untar;

        cd $AMHDir/packages/untar/$PhpVersion;
        if [ "$InstallModel" == '1' ]; then
            ./configure --prefix=/usr/local/php --enable-fpm --with-fpm-user=www --with-fpm-group=www --with-config-file-path=/etc --with-config-file-scan-dir=/etc/php.d --enable-ftp --enable-gd-native-ttf --enable-mbstring --enable-bcmath --enable-shmop --enable-soap --enable-exif --enable-sysvsem --enable-inline-optimization --enable-mbregex --enable-xml --enable-opcache=no --with-zlib --with-curl --with-mhash --with-mcrypt --with-gd --with-jpeg-dir --with-png-dir --with-freetype-dir --enable-pcntl --enable-sockets --with-xmlrpc --with-gettext --with-iconv=/usr/local/libiconv --with-zlib=/usr/local/zlib --with-openssl=/usr/local/openssl --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-libxml-dir=/usr --without-pear --disable-ipv6 --disable-fileinfo --disable-rpath --disable-debug $PHPDisable;
        fi;
        make ZEND_EXTRA_LIBS='-liconv';
        make -j $Cpunum;
        make install;
        
        cp $AMHDir/conf/php.ini /etc/php.ini;
        cp $AMHDir/conf/php /root/amh/php;
        cp $AMHDir/conf/php-fpm.conf /usr/local/php/etc/php-fpm.conf;
        cp $AMHDir/conf/php-fpm-template.conf /usr/local/php/etc/php-fpm-template.conf;
        chmod +x /root/amh/php;
        mkdir /etc/php.d;
        mkdir /usr/local/php/etc/fpm;
        mkdir /usr/local/php/var/run/pid;
        touch /usr/local/php/etc/fpm/amh.conf;
        /usr/local/php/sbin/php-fpm;

        ln -s /usr/local/php/bin/php /usr/bin/php;
        ln -s /usr/local/php/bin/phpize /usr/bin/phpize;
        ln -s /usr/local/php/sbin/php-fpm /usr/bin/php-fpm;

        if [ $RamTotal -gt 1024 -a $RamTotal -le 1500 ]; then
            Memory_limit=192
        elif [ $RamTotal -gt 1500 -a $RamTotal -le 3500 ]; then
            Memory_limit=256
        elif [ $RamTotal -gt 3500 -a $RamTotal -le 4500 ]; then
            Memory_limit=320
        elif [ $RamTotal -gt 4500 ]; then
            Memory_limit=448
        else
            Memory_limit=128
        fi;

        sed -i "s@^memory_limit.*@memory_limit = ${Memory_limit}M@" /etc/php.ini;
        sed -i "s@^;opcache.memory_consumption.*@opcache.memory_consumption=$Memory_limit@" /etc/php.ini;

        if [ $RamTotal -le 3000 ]; then
            sed -i "s@^pm.max_children.*@pm.max_children = $(($RamTotal/2/20))@" /usr/local/php/etc/php-fpm.conf;
            sed -i "s@^pm.start_servers.*@pm.start_servers = $(($RamTotal/2/30))@" /usr/local/php/etc/php-fpm.conf;
            sed -i "s@^pm.min_spare_servers.*@pm.min_spare_servers = $(($RamTotal/2/40))@" /usr/local/php/etc/php-fpm.conf;
            sed -i "s@^pm.max_spare_servers.*@pm.max_spare_servers = $(($RamTotal/2/20))@" /usr/local/php/etc/php-fpm.conf;
        elif [ $RamTotal -gt 3000 -a $RamTotal -le 4500 ]; then
            sed -i "s@^pm.max_children.*@pm.max_children = 80@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.start_servers.*@pm.start_servers = 50@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.min_spare_servers.*@pm.min_spare_servers = 40@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.max_spare_servers.*@pm.max_spare_servers = 80@" /usr/local/php/etc/php-fpm.conf;
        elif [ $RamTotal -gt 4500 -a $RamTotal -le 6500 ]; then
                sed -i "s@^pm.max_children.*@pm.max_children = 90@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.start_servers.*@pm.start_servers = 60@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.min_spare_servers.*@pm.min_spare_servers = 50@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.max_spare_servers.*@pm.max_spare_servers = 90@" /usr/local/php/etc/php-fpm.conf;
        elif [ $RamTotal -gt 6500 -a $RamTotal -le 8500 ]; then
                sed -i "s@^pm.max_children.*@pm.max_children = 100@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.start_servers.*@pm.start_servers = 70@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.min_spare_servers.*@pm.min_spare_servers = 60@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.max_spare_servers.*@pm.max_spare_servers = 100@" /usr/local/php/etc/php-fpm.conf;
        elif [ $RamTotal -gt 8500 ]; then
                sed -i "s@^pm.max_children.*@pm.max_children = 120@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.start_servers.*@pm.start_servers = 80@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.min_spare_servers.*@pm.min_spare_servers = 70@" /usr/local/php/etc/php-fpm.conf;
                sed -i "s@^pm.max_spare_servers.*@pm.max_spare_servers = 120@" /usr/local/php/etc/php-fpm.conf;
        fi;

        echo "[OK] ${PhpVersion} install completed.";
    else
        echo '[OK] PHP is installed.';
    fi;
}

function InstallNginx()
{
    # [dir] /usr/local/nginx
    echo "[${NginxVersion} Installing] ************************************************** >>";
    if [ ! -d /usr/local/nginx ]; then
        # [dir] /usr/local/src/ngx_cache_purge*
        Downloadfile "${NgxcachepurgeVersion}.tar.gz" "${GetUrl}/${NgxcachepurgeVersion}.tar.gz";
        rm -rf /usr/local/src/$NgxcachepurgeVersion;
        echo "tar -zxf ${NgxcachepurgeVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$NgxcachepurgeVersion.tar.gz -C /usr/local/src;

        # [dir] /usr/local/src/ngx_pagespeed*
        Downloadfile "${NgxpagespeedVersion}.tar.bz2" "${GetUrl}/${NgxpagespeedVersion}.tar.bz2";
        rm -rf /usr/local/src/$NgxpagespeedVersion;
        echo "tar -jxf ${NgxpagespeedVersion}.tar.bz2 ing...";
        tar -jxf $AMHDir/packages/$NgxpagespeedVersion.tar.bz2 -C /usr/local/src;

        # [dir] /usr/local/src/ngx_http_substitutions_filter_module
        cd /usr/local/src
        git clone git://github.com/yaoweibin/ngx_http_substitutions_filter_module.git

        Downloadfile "${NginxVersion}.tar.gz" "${GetUrl}/${NginxVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$NginxVersion;
        echo "tar -zxf ${NginxVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$NginxVersion.tar.gz -C $AMHDir/packages/untar;

        sed -i "s#/usr/local#/usr/local/gperftools#" $AMHDir/packages/untar/$NginxVersion/auto/lib/google-perftools/conf;
    
        cd $AMHDir/packages/untar/$NginxVersion;
        ./configure --prefix=/usr/local/nginx --user=www --group=www --with-google_perftools_module --with-http_ssl_module --with-http_spdy_module --with-http_concat_module --with-http_gzip_static_module --with-http_realip_module --with-http_sub_module --with-http_dav_module --with-http_flv_module --with-http_mp4_module --with-http_gunzip_module --with-http_stub_status_module --with-http_addition_module --with-http_secure_link_module --without-mail_pop3_module --without-mail_imap_module --without-mail_smtp_module --with-mail --with-mail_ssl_module --with-ipv6 --with-pcre=/usr/local/src/$PcreVersion --with-zlib=/usr/local/src/$ZlibVersion --with-openssl=/usr/local/src/$OpensslVersion --without-http_uwsgi_module --without-http_scgi_module --add-module=/usr/local/src/${NgxcachepurgeVersion} --add-module=/usr/local/src/${NgxpagespeedVersion} --add-module=/usr/local/src/ngx_http_substitutions_filter_module;
        make -j $Cpunum;
        make install;

        mkdir -p /home/wwwroot/index /home/backup /usr/local/nginx/conf/vhost/ /usr/local/nginx/conf/vhost_stop/ /usr/local/nginx/conf/rewrite/;
        mkdir -p /tmp/ngx_pagespeed_cache /tmp/client_body_temp /tmp/nginx_fastcgi_temp /tmp/nginx_proxy_temp;
        chown www:www -R /home/wwwroot/index;
        touch /usr/local/nginx/conf/rewrite/amh.conf;

        cp $AMHDir/conf/nginx.conf /usr/local/nginx/conf/nginx.conf;
        cp $AMHDir/conf/nginx-host.conf /usr/local/nginx/conf/nginx-host.conf;
        cp $AMHDir/conf/fcgi.conf /usr/local/nginx/conf/fcgi.conf;
        cp $AMHDir/conf/fcgi-host.conf /usr/local/nginx/conf/fcgi-host.conf;
        cp $AMHDir/conf/pagespeed.conf /usr/local/nginx/conf/pagespeed.conf;
        cp $AMHDir/conf/pagespeed_handler.conf /usr/local/nginx/conf/pagespeed_handler.conf;
        cp $AMHDir/conf/pagespeed_statslog.conf /usr/local/nginx/conf/pagespeed_statslog.conf;
        cp $AMHDir/conf/nginx /root/amh/nginx;
        cp $AMHDir/conf/host /root/amh/host;
        chmod +x /root/amh/nginx;
        chmod +x /root/amh/host;
        sed -i 's/www.amysql.com/'$Domain'/g' /usr/local/nginx/conf/nginx.conf;

        if [ $Cpunum == 2 ]; then
                sed -i 's@^worker_processes.*@worker_processes 2;\nworker_cpu_affinity 10 01;@' /usr/local/nginx/conf/nginx.conf;
        elif [ $Cpunum == 3 ]; then
                sed -i 's@^worker_processes.*@worker_processes 3;\nworker_cpu_affinity 100 010 001;@' /usr/local/nginx/conf/nginx.conf;
        elif [ $Cpunum == 4 ]; then
                sed -i 's@^worker_processes.*@worker_processes 4;\nworker_cpu_affinity 1000 0100 0010 0001;@' /usr/local/nginx/conf/nginx.conf;
        elif [ $Cpunum == 6 ]; then
                sed -i 's@^worker_processes.*@worker_processes 6;\nworker_cpu_affinity 100000 010000 001000 000100 000010 000001;@' /usr/local/nginx/conf/nginx.conf;
        elif [ $Cpunum == 8 ]; then
                sed -i 's@^worker_processes.*@worker_processes 8;\nworker_cpu_affinity 10000000 01000000 00100000 00010000 00001000 00000100 00000010 00000001;@' /usr/local/nginx/conf/nginx.conf;
        else
                echo Google worker_cpu_affinity;
        fi;

        cd /home/wwwroot/index;
        mkdir -p tmp etc/rsa bin usr/sbin log;
        touch etc/upgrade.conf;
        chown mysql:mysql etc/rsa;
        chmod 777 tmp;
        [ "$SysBit" == '64' ] && mkdir lib64 || mkdir lib;
        /usr/local/nginx/sbin/nginx;
        /usr/local/php/sbin/php-fpm;
        ln -s /usr/local/nginx/sbin/nginx /usr/bin/nginx;

        echo "[OK] ${NginxVersion} install completed.";
    else
        echo '[OK] Nginx is installed.';
    fi;
}

function InstallPureFTPd()
{
    # [dir] /etc/  /usr/local/bin  /usr/local/sbin
    echo "[${PureFTPdVersion} Installing] ************************************************** >>";
    if [ ! -f /etc/pure-ftpd.conf ]; then
        Downloadfile "${PureFTPdVersion}.tar.gz" "${GetUrl}/${PureFTPdVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$PureFTPdVersion;
        echo "tar -zxf ${PureFTPdVersion}.tar.gz ing...";
        tar -zxf $AMHDir/packages/$PureFTPdVersion.tar.gz -C $AMHDir/packages/untar;

        cd $AMHDir/packages/untar/$PureFTPdVersion;
        ./configure --with-puredb --with-quotas --with-throttling --with-ratios --with-peruserlimits;
        make -j $Cpunum;
        make install;
        cp contrib/redhat.init /usr/local/sbin/redhat.init;
        chmod 755 /usr/local/sbin/redhat.init;

        cp $AMHDir/conf/pure-ftpd.conf /etc;
        cp configuration-file/pure-config.pl /usr/local/sbin/pure-config.pl;
        chmod 744 /etc/pure-ftpd.conf;
        chmod 755 /usr/local/sbin/pure-config.pl;
        /usr/local/sbin/redhat.init start;

        groupadd ftpgroup;
        useradd -d /home/wwwroot/ -s /sbin/nologin -g ftpgroup ftpuser;

        cp $AMHDir/conf/ftp /root/amh/ftp;
        chmod +x /root/amh/ftp;

        /sbin/iptables-save > /etc/amh-iptables;
        sed -i '/--dport 21 -j ACCEPT/d' /etc/amh-iptables;
        sed -i '/--dport 80 -j ACCEPT/d' /etc/amh-iptables;
        sed -i '/--dport 8888 -j ACCEPT/d' /etc/amh-iptables;
        sed -i '/--dport 10100:10110 -j ACCEPT/d' /etc/amh-iptables;
        /sbin/iptables-restore < /etc/amh-iptables;
        /sbin/iptables -I INPUT -p tcp --dport 21 -j ACCEPT;
        /sbin/iptables -I INPUT -p tcp --dport 80 -j ACCEPT;
        /sbin/iptables -I INPUT -p tcp --dport 8888 -j ACCEPT;
        /sbin/iptables -I INPUT -p tcp --dport 10100:10110 -j ACCEPT;
        /sbin/iptables-save > /etc/amh-iptables;
        echo 'IPTABLES_MODULES="ip_conntrack_ftp"' >>/etc/sysconfig/iptables-config;

        touch /etc/pureftpd.passwd;
        chmod 774 /etc/pureftpd.passwd;
        echo "[OK] ${PureFTPdVersion} install completed.";
    else
        echo '[OK] PureFTPd is installed.';
    fi;
}

function InstallAMH()
{
    # [dir] /home/wwwroot/index/web
    echo "[${AMHVersion} Installing] ************************************************** >>";
    if [ ! -d /home/wwwroot/index/web ]; then
        Downloadfile "${AMHVersion}.tar.gz" "${GetUrl}/${AMHVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$AMHVersion;
        echo "tar -xf ${AMHVersion}.tar.gz ing...";
        tar -xf $AMHDir/packages/$AMHVersion.tar.gz -C $AMHDir/packages/untar;

        cp -r $AMHDir/packages/untar/$AMHVersion /home/wwwroot/index/web;

        gcc -o /bin/amh -Wall $AMHDir/conf/amh.c;
        chmod 4775 /bin/amh;
        cp -a $AMHDir/conf/amh-backup.conf /home/wwwroot/index/etc;
        cp -a $AMHDir/conf/html /home/wwwroot/index/etc;
        cp $AMHDir/conf/{backup,revert,BRssh,BRftp,info,SetParam,module,crontab,upgrade} /root/amh;
        cp -a $AMHDir/conf/modules /root/amh;
        chmod +x /root/amh/backup /root/amh/revert /root/amh/BRssh /root/amh/BRftp /root/amh/info /root/amh/SetParam /root/amh/module /root/amh/crontab /root/amh/upgrade;

        SedMysqlPass=${MysqlPass//&/\\\&};
        SedMysqlPass=${SedMysqlPass//\'/\\\\\'};
        sed -i "s/'MysqlPass'/'${SedMysqlPass}'/g" /home/wwwroot/index/web/Amysql/Config.php;
        chown www:www /home/wwwroot/index/web/Amysql/Config.php;

        SedAMHPass=${AMHPass//&/\\\&};
        SedAMHPass=${SedAMHPass//\'/\\\\\\\\\'\'};
        sed -i "s/'AMHPass_amysql-amh'/'${SedAMHPass}_amysql-amh'/g" $AMHDir/conf/amh.sql;
        /usr/local/mysql/bin/mysql -u root -p$MysqlPass < $AMHDir/conf/amh.sql;

        echo "[OK] ${AMHVersion} install completed.";
    else
        echo '[OK] AMH is installed.';
    fi;
}

function InstallAMS()
{
    # [dir] /home/wwwroot/index/web/ams
    echo "[${AMSVersion} Installing] ************************************************** >>";
    if [ ! -d /home/wwwroot/index/web/ams ]; then
        Downloadfile "${AMSVersion}.tar.gz" "${GetUrl}/${AMSVersion}.tar.gz";
        rm -rf $AMHDir/packages/untar/$AMSVersion;
        echo "tar -xf ${AMSVersion}.tar.gz ing...";
        tar -xf $AMHDir/packages/$AMSVersion.tar.gz -C $AMHDir/packages/untar;

        cp -r $AMHDir/packages/untar/$AMSVersion /home/wwwroot/index/web/ams;
        chown www:www -R /home/wwwroot/index/web/ams/View/DataFile;
        echo "[OK] ${AMSVersion} install completed.";
    else
        echo '[OK] AMS is installed.';
    fi;
}

function InstallSafeSshd()
{
    echo "[neusshbl Installing] ************************************************** >>";
    if [ ! -a $NeusshblFiLe ] && [ ! -L $NeusshblLink ]; then
        LIBWRAP=`ldd \`which sshd\` | grep libwrap | wc -l`;
        if [ $LIBWRAP -ge 1 ]; then
            cd /usr/local/bin/;
            wget antivirus.neu.edu.cn/ssh/soft/fetch_neusshbl.sh;
            chmod +x fetch_neusshbl.sh;
            cd /etc/cron.hourly/;
            ln -s /usr/local/bin/fetch_neusshbl.sh .;
            ./fetch_neusshbl.sh;
        else
            echo '[Error] Your system does not have TCP Wrappers!';
        fi;
    else
        echo '[OK] neusshbl is installed!';
    fi; 
}


# AMH Installing ****************************************************************************
CheckSystem;
ConfirmInstall;
SelectLocation;
InputDomain;
InputMysqlPass;
InputAMHPass;
Timezone;
CloseSelinux;
DeletePackages;
InstallBasePackages;
InstallReady;
InstallLibiconv;
InstallGperftools;
InstallPcre;
InstallZlib;
InstallOpenssl;
InstallMysql;
InstallPhp;
InstallNginx;
InstallPureFTPd;
InstallAMH;
InstallAMS;
InstallSafeSshd;

if [ -s /usr/local/nginx ] && [ -s /usr/local/php ] && [ -s /usr/local/mysql ]; then

cp $AMHDir/conf/amh-start /etc/init.d/amh-start;
chmod 775 /etc/init.d/amh-start;
if [ "$SysName" == 'CentOS' ]; then
    chkconfig --add amh-start;
    chkconfig amh-start on;
else
    update-rc.d -f amh-start defaults;
fi;

/etc/init.d/amh-start;
rm -rf $AMHDir;

echo '================================================================';
    echo '[AMH] Congratulations, AMH 4.2 install completed.';
    echo "AMH Management: http://${Domain}:8888";
    echo 'User:admin';
    echo "Password:${AMHPass}";
    echo "MySQL Password:${MysqlPass}";
    echo '';
    echo '******* SSH Management *******';
    echo 'Host: amh host';
    echo 'PHP: amh php';
    echo 'Nginx: amh nginx';
    echo 'MySQL: amh mysql';
    echo 'FTP: amh ftp';
    echo 'Backup: amh backup';
    echo 'Revert: amh revert';
    echo 'SetParam: amh SetParam';
    echo 'Module : amh module';
    echo 'Crontab : amh crontab';
    echo 'Upgrade : amh upgrade';
    echo 'Info: amh info';
    echo '';
    echo '******* SSH Dirs *******';
    echo 'WebSite: /home/wwwroot';
    echo 'Nginx: /usr/local/nginx';
    echo 'PHP: /usr/local/php';
    echo 'MySQL: /usr/local/mysql';
    echo 'MySQL-Data: /home/mysql_data';
    echo '';
    echo "Start time: ${StartDate}";
    echo "Completion time: $(date) (Use: $[($(date +%s)-StartDateSecond)/60] minute)";
    echo 'More help please visit: https://github.com/maicong/amh-4.2/wiki';
echo '================================================================';
else
    echo 'Sorry, Failed to install AMH';
    echo 'Please contact us: https://github.com/maicong/amh-4.2/issues';
fi;
