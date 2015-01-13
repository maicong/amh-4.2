#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH

clear;
echo '================================================================';
echo ' [LNMP/Nginx] Amysql Host - AMH 4.2 - 麦葱修改版 ';
echo ' http://Amysql.com & http://www.yuxiaoxi.com';
echo '================================================================';


# VAR ***************************************************************************************
AMHDir='/home/amh_install/';
SysName='';
SysBit='';
Cpunum='';
RamTotal='';
RamSwap='';
InstallModel='';
Domain=`ifconfig  | grep 'inet addr:'| egrep -v ":192.168|:172.1[6-9].|:172.2[0-9].|:172.3[0-2].|:10.|:127." | cut -d: -f2 | awk '{ print $1}'`;
MysqlPass='';
AMHPass='';
StartDate='';
StartDateSecond='';
PHPDisable='';

# Version
AMSVersion='ams-1.5.0107-02';
AMHVersion='amh-4.2';
LibiconvVersion='libiconv-1.14';
PcreVersion='pcre-8.35';
ZlibVersion='zlib-1.2.8';
OpensslVersion='openssl-1.0.1k';
JemallocVersion='jemalloc-3.6.0';
MysqlVersion='mysql-5.5.34';
PhpVersion='php-5.6.4';
PureFTPdVersion='pure-ftpd-1.0.36';
NginxVersion='tengine-2.1.0';
GetUrl='u1.cdn.yuxiaoxi.com/linux/amh-4.2';
NeusshblFiLe='/usr/local/bin/fetch_neusshbl.sh';
NeusshblLink='/etc/cron.hourly/fetch_neusshbl.sh';

# Function List	*****************************************************************************
function CheckSystem()
{
	[ $(id -u) != '0' ] && echo '[错误] 请使用 root 权限安装 AMH' && exit;
	egrep -i "centos" /etc/issue && SysName='centos';
	egrep -i "debian" /etc/issue && SysName='debian';
	egrep -i "ubuntu" /etc/issue && SysName='ubuntu';
	[ "$SysName" == ''  ] && echo '[错误] 你的系统不支持安装 AMH' && exit;

	SysBit='32' && [ `getconf WORD_BIT` == '32' ] && [ `getconf LONG_BIT` == '64' ] && SysBit='64';
	Cpunum=`cat /proc/cpuinfo | grep 'processor' | wc -l`;
	RamTotal=`free -m | grep 'Mem' | awk '{print $2}'`;
	RamSwap=`free -m | grep 'Swap' | awk '{print $2}'`;
	echo "Server ${Domain}";
	echo "${SysBit}Bit, ${Cpunum}*CPU, ${RamTotal}MB*RAM, ${RamSwap}MB*Swap";
	echo '================================================================';
	
	RamSum=$[$RamTotal+$RamSwap];
	[ "$SysBit" == '32' ] && [ "$RamSum" -lt '250' ] && \
	echo -e "[错误] 没有足够的内存安装 AMH. \n(32位系统需要的内存: ${RamTotal}MB*RAM + ${RamSwap}MB*Swap > 250MB)" && exit;

	if [ "$SysBit" == '64' ] && [ "$RamSum" -lt '480' ];  then
		echo -e "[错误] 没有足够的内存安装 AMH. \n(64位系统需要的内存: ${RamTotal}MB*RAM + ${RamSwap}MB*Swap > 480MB)";
		[ "$RamSum" -gt '250' ] && echo "[提示] 请使用32位系统.";
		exit;
	fi;
	
	[ "$RamSum" -lt '600' ] && PHPDisable='--disable-fileinfo';
}

function ConfirmInstall()
{
	echo "[提示] 确认 安装/卸载 AMH? 请选择: (1~3)"
	select selected in '安装 AMH 4.2' '卸载 AMH 4.2' '退出'; do break; done;
	[ "$selected" == '退出' ] && echo '退出安装.' && exit;
		
	if [ "$selected" == '安装 AMH 4.2' ]; then
		InstallModel='1';
	elif [ "$selected" == '卸载 AMH 4.2' ]; then
		Uninstall;
	else
		ConfirmInstall;
		return;
	fi;

	echo "[完成] 你选择了: ${selected}";
}

function InputDomain()
{
	if [ "$Domain" == '' ]; then
		echo '[错误] 服务器IP为空.';
		read -p '[提示] 请输入服务器IP:' Domain;
		[ "$Domain" == '' ] && InputDomain;
	fi;
	[ "$Domain" != '' ] && echo '[完成] 你的服务器IP是:' && echo $Domain;
}


function InputMysqlPass()
{
	read -p '[提示] 请输入 MySQL 密码:' MysqlPass;
	if [ "$MysqlPass" == '' ]; then
		echo '[错误] MySQL 密码为空.';
		InputMysqlPass;
	else
		echo '[完成] 你的 MySQL 密码是:';
		echo $MysqlPass;
	fi;
}


function InputAMHPass()
{
	read -p '[提示] 请输入 AMH 密码:' AMHPass;
	if [ "$AMHPass" == '' ]; then
		echo '[错误] AMH 密码为空.';
		InputAMHPass;
	else
		echo '[完成] 你的 AMH 密码是:';
		echo $AMHPass;
	fi;
}


function Timezone()
{
	rm -rf /etc/localtime;
	ln -s /usr/share/zoneinfo/Asia/Shanghai /etc/localtime;

	echo '[NTP(时间服务器) 安装中] ******************************** >>';
	[ "$SysName" == 'centos' ] && yum install -y ntp || apt-get install -y ntpdate;
	ntpdate -u pool.ntp.org;
	StartDate=$(date);
	StartDateSecond=$(date +%s);
	echo "起始时间: ${StartDate}";
}


function CloseSelinux()
{
	[ -s /etc/selinux/config ] && sed -i 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config;
}

function DeletePackages()
{
	if [ "$SysName" == 'centos' ]; then
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
	if [ "$SysName" == 'centos' ]; then
		echo '[yum-fastestmirror 安装中] ************************************************** >>';
		yum -y install yum-fastestmirror;

		cp /etc/yum.conf /etc/yum.conf.lnmp
		sed -i 's:exclude=.*:exclude=:g' /etc/yum.conf
		for packages in gcc gcc-c++ ncurses-devel libxml2 libxml2-devel openssl-devel curl-devel gd gd-devel libjpeg libjpeg-devel libpng libpng-devel libpng10 libpng10-devel autoconf kernel-devel pcre-devel libtool libtool-libs freetype freetype-devel zlib zlib-devel zip unzip wget crontabs iptables file bison cmake patch mlocate flex diffutils automake make readline-devel glibc-devel glibc-static glib2 glib2-devel bzip2 bzip2-devel gettext-devel libcap-devel logrotate ftp expect; do 
			echo "[${packages} 安装中] ************************************************** >>";
			yum -y install $packages; 
		done;
		mv -f /etc/yum.conf.lnmp /etc/yum.conf;
	else
		apt-get remove -y apache2 apache2-doc apache2-utils apache2.2-common apache2.2-bin apache2-mpm-prefork apache2-doc apache2-mpm-worker mysql-client mysql-server mysql-common php;
		killall apache2;
		apt-get update;
		for packages in build-essential gcc g++ cmake make ntp logrotate automake patch autoconf autoconf2.13 re2c wget flex cron libzip-dev libc6-dev rcconf bison cpp binutils unzip tar bzip2 libncurses5-dev libncurses5 libtool libevent-dev libpcre3 libpcre3-dev libpcrecpp0 libssl-dev zlibc libsasl2-dev libxml2 libxml2-dev libltdl3-dev libltdl-dev zlib1g zlib1g-dev libbz2-1.0 libbz2-dev libglib2.0-0 libglib2.0-dev libpng3 libfreetype6 libfreetype6-dev libjpeg62 libjpeg62-dev libjpeg-dev libpng-dev libpng12-0 libpng12-dev curl libcurl3 libpq-dev libpq5 gettext libcurl4-gnutls-dev  libcurl4-openssl-dev libcap-dev ftp expect; do
			echo "[${packages} 安装中] ************************************************** >>";
			apt-get install -y $packages --force-yes;apt-get -fy install;apt-get -y autoremove; 
		done;
	fi;
}


function Downloadfile()
{
	randstr=$(date +%s);
	cd $AMHDir/packages;

	if [ -s $1 ]; then
		echo "[完成] 已发现 $1.";
	else
		echo "[提示] 没有发现 $1, 下载中......";
		if ! wget -c --tries=3 ${2}?${randstr} ; then
			echo "[错误] 下载失败 : $1, 请检查 $2 ";
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
	chmod +Rw $AMHDir/packages;

	mkdir -p /root/amh/;
	chmod +Rw /root/amh;

	cd $AMHDir/packages;
	wget http://${GetUrl}/conf.zip;
	unzip conf.zip -d $AMHDir/conf;
}


# Install Function  *********************************************************

function Uninstall()
{
	amh host list 2>/dev/null;
	echo -e "\033[41m\033[37m[注意] 请先备份你的数据. 卸载将会删除所有数据!!! \033[0m ";
	read -p '[提示] 现在就备份吗? : (y/n)' confirmBD;
	[ "$confirmBD" != 'y' -a "$confirmBD" != 'n' ] && exit;
	[ "$confirmBD" == 'y' ] && amh backup;
	echo '=============================================================';

	read -p '[提示] 确定卸载(删除所有数据)? : (y/n)' confirmUN;
	[ "$confirmUN" != 'y' ] && exit;
	amh mysql stop 2>/dev/null;
	amh php stop 2>/dev/null;
	amh nginx stop 2>/dev/null;

	killall nginx;
	killall mysqld;
	killall pure-ftpd;
	killall php-cgi;
	killall php-fpm;
    
	[ "$SysName" == 'centos' ] && chkconfig amh-start off || update-rc.d -f amh-start remove;
	rm -rf /etc/init.d/amh-start;
	rm -rf /usr/local/libiconv;
    rm -rf /usr/local/pcre;
    rm -rf /usr/local/zlib;
    rm -rf /usr/local/openssl;
    rm -rf /usr/local/ssl;
    rm -rf /usr/local/jemalloc;
	rm -rf /usr/local/nginx/;
	rm -rf /usr/local/pcre-src;
    rm -rf /usr/local/zlib-src;
    rm -rf /usr/local/openssl-src;
    rm -rf /usr/local/jemalloc-src;
	for line in `ls /root/amh/modules`; do
		amh module $line uninstall;
	done;
	rm -rf /etc/ld.so.conf.d/libiconv.conf /etc/ld.so.conf.d/pcre.conf /etc/ld.so.conf.d/zlib.conf /etc/ld.so.conf.d/jemalloc.conf;
	rm -rf /usr/local/mysql/ /etc/my.cnf  /etc/ld.so.conf.d/mysql.conf /usr/bin/mysql /var/lock/subsys/mysql /var/spool/mail/mysql;
	rm -rf /usr/local/php/ /usr/lib/php /etc/php.ini /etc/php.d /usr/local/zend;
	rm -rf /home/wwwroot/;
	rm -rf /etc/pure-ftpd.conf /etc/pam.d/ftp /usr/local/sbin/pure-ftpd /etc/pureftpd.passwd /etc/amh-iptables;
	rm -rf /etc/logrotate.d/nginx /root/.mysqlroot;
	rm -rf /root/amh /bin/amh;
	rm -rf $AMHDir;
	rm -f /usr/bin/{mysqld_safe,myisamchk,mysqldump,mysqladmin,mysql,nginx,php-fpm,phpize,php};

	echo '[完成] 成功卸载 AMH.';
	exit;
}

function InstallLibiconv()
{
	echo "[${LibiconvVersion} 安装中] ************************************************** >>";
	if [ ! -d /usr/local/libiconv ]; then
		Downloadfile "${LibiconvVersion}.tar.gz" "http://${GetUrl}/${LibiconvVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$LibiconvVersion;
		echo "正在解压 ${LibiconvVersion}.tar.gz ...";
		tar -zxf $AMHDir/packages/$LibiconvVersion.tar.gz -C $AMHDir/packages/untar;

		cd $AMHDir/packages/untar/$LibiconvVersion;
		./configure --prefix=/usr/local/libiconv;
		make;
		make install;
		echo '/usr/local/libiconv/lib' > /etc/ld.so.conf.d/libiconv.conf;
		/sbin/ldconfig;
		echo "[完成] ${LibiconvVersion} 安装完成.";
	else
		echo '[完成] libiconv 已经安装!';
	fi;
}

function InstallPcre()
{
	echo "[${PcreVersion} 安装中] ************************************************** >>";
	if [ ! -d /usr/local/pcre ]; then
		Downloadfile "${PcreVersion}.tar.gz" "http://${GetUrl}/${PcreVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$PcreVersion;
		echo "正在解压 ${PcreVersion}.tar.gz ...";
		if [ ! -d /usr/local/pcre-src ]; then
			mkdir /usr/local/pcre-src;
		fi;
		tar -zxf $AMHDir/packages/$PcreVersion.tar.gz -C /usr/local/pcre-src;
	
		cd /usr/local/pcre-src;
		./configure --prefix=/usr/local/pcre;
		make;
		make install;
		echo '/usr/local/pcre/lib' > /etc/ld.so.conf.d/pcre.conf;
		/sbin/ldconfig;
		echo "[完成] ${PcreVersion} 安装完成.";
	else
		echo '[完成] pcre 已经安装!';
	fi;
}

function InstallZlib()
{
	echo "[${ZlibVersion} 安装中] ************************************************** >>";
	if [ ! -d /usr/local/zlib ]; then
		Downloadfile "${ZlibVersion}.tar.gz" "http://${GetUrl}/${ZlibVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$ZlibVersion;
		echo "正在解压 ${ZlibVersion}.tar.gz ...";
		if [ ! -d /usr/local/zlib-src ]; then
			mkdir /usr/local/zlib-src;
		fi;
		tar -zxf $AMHDir/packages/$ZlibVersion.tar.gz -C /usr/local/zlib-src;

		cd /usr/local/zlib-src;
		./configure --prefix=/usr/local/zlib;
		make;
		make install;
		echo '/usr/local/zlib/lib' > /etc/ld.so.conf.d/zlib.conf;
		/sbin/ldconfig;
		echo "[完成] ${ZlibVersion} 安装完成.";
	else
		echo '[完成] zlib 已经安装!';
	fi;
}

function InstallOpenssl()
{
	echo "[${OpensslVersion} 安装中] ************************************************** >>";
	if [ ! -d /usr/local/openssl ]; then
		Downloadfile "${OpensslVersion}.tar.gz" "http://${GetUrl}/${OpensslVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$OpensslVersion;
		echo "正在解压 ${OpensslVersion}.tar.gz ...";
		if [ ! -d /usr/local/openssl-src ]; then
			mkdir /usr/local/openssl-src;
		fi;
		tar -zxf $AMHDir/packages/$OpensslVersion.tar.gz -C /usr/local/openssl-src;

		cd /usr/local/openssl-src;
		./config --prefix=/usr/local/openssl --openssldir=/usr/local/ssl;
		make;
		make install;
		echo "[完成] ${OpensslVersion} 安装完成.";
	else
		echo '[完成] openssl 已经安装!';
	fi;
}

function InstallJemalloc()
{
	echo "[${JemallocVersion} 安装中] ************************************************** >>";
	if [ ! -d /usr/local/jemalloc ]; then
		Downloadfile "${JemallocVersion}.tar.gz" "http://${GetUrl}/${JemallocVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$JemallocVersion;
		echo "正在解压 ${JemallocVersion}.tar.gz ...";
		if [ ! -d /usr/local/jemalloc-src ]; then
			mkdir /usr/local/jemalloc-src;
		fi;
		tar -zxf $AMHDir/packages/$JemallocVersion.tar.gz -C $/usr/local/jemalloc-src;
	
		cd /usr/local/jemalloc-src;
		./configure --prefix=/usr/local/jemalloc;
		make;
		make install;
		echo '/usr/local/jemalloc/lib' > /etc/ld.so.conf.d/jemalloc.conf;
		/sbin/ldconfig;
		echo "[完成] ${JemallocVersion} 安装完成.";
	else
		echo '[完成] jemalloc 已经安装!';
	fi;
}

function InstallSafeSshd()
{
    echo "[SSH IP黑名单 安装中] ************************************************** >>";
    if [ ! -a $NeusshblFiLe ] && [ ! -L $NeusshblLink ]; then
        LIBWRAP=`ldd \`which sshd\` | grep libwrap | wc -l`;
        if [ $LIBWRAP -ge 1 ]; then
            cd /usr/local/bin/;
            wget antivirus.neu.edu.cn/ssh/soft/fetch_neusshbl.sh;
            chmod +x fetch_neusshbl.sh;
            cd /etc/cron.hourly/;
            ln -s /usr/local/bin/fetch_neusshbl.sh .;
            ./fetch_neusshbl.sh;
        fi;
    else
        echo '[完成] SSH IP黑名单 已经安装.';
    fi; 
}

function InstallMysql()
{
	# [dir] /usr/local/mysql/
	echo "[${MysqlVersion} 安装中] ************************************************** >>";
	if [ ! -f /usr/local/mysql/bin/mysql ]; then
		Downloadfile "${MysqlVersion}.tar.gz" "http://${GetUrl}/${MysqlVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$MysqlVersion;
		echo "正在解压 ${MysqlVersion}.tar.gz ...";
		tar -zxf $AMHDir/packages/$MysqlVersion.tar.gz -C $AMHDir/packages/untar;
	
		cd $AMHDir/packages/untar/$MysqlVersion;
		groupadd mysql;
		useradd -s /sbin/nologin -g mysql mysql;
		cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql -DDEFAULT_CHARSET=utf8 -DDEFAULT_COLLATION=utf8_general_ci -DWITH_EXTRA_CHARSETS=complex -DWITH_READLINE=1 -DENABLED_LOCAL_INFILE=1;
		#http://forge.mysql.com/wiki/Autotools_to_CMake_Transition_Guide
		make -j $Cpunum;
		make install;
		chmod +w /usr/local/mysql;
		chown -R mysql:mysql /usr/local/mysql;

		rm -f /etc/mysql/my.cnf /usr/local/mysql/etc/my.cnf;
		cp $AMHDir/conf/my.cnf /etc/my.cnf;
		cp $AMHDir/conf/mysql /root/amh/mysql;
		chmod +x /root/amh/mysql;
		/usr/local/mysql/scripts/mysql_install_db --user=mysql --defaults-file=/etc/my.cnf --basedir=/usr/local/mysql --datadir=/usr/local/mysql/data;
		

# EOF **********************************
cat > /etc/ld.so.conf.d/mysql.conf<<EOF
/usr/local/mysql/lib/mysql
/usr/local/lib
EOF
# **************************************

		ldconfig;
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

		sed -i 's@executing mysqld_safe@executing mysqld_safe\nexport LD_PRELOAD=/usr/local/jemalloc/lib/libjemalloc.so@' /usr/local/mysql/bin/mysqld_safe

		/usr/local/mysql/bin/mysqladmin password $MysqlPass;
		rm -rf /usr/local/mysql/data/test;

# EOF **********************************
mysql -hlocalhost -uroot -p$MysqlPass <<EOF
USE mysql;
DELETE FROM user WHERE User!='root' OR (User = 'root' AND Host != 'localhost');
UPDATE user set password=password('$MysqlPass') WHERE User='root';
DROP USER ''@'%';
FLUSH PRIVILEGES;
EOF
# **************************************
		echo "[完成] ${MysqlVersion} 安装完成.";
	else
		echo '[完成] MySQL 已经安装.';
	fi;

}

function InstallPhp()
{
	# [dir] /usr/local/php
	echo "[${PhpVersion} 安装中] ************************************************** >>";
	if [ ! -d /usr/local/php ]; then
		Downloadfile "${PhpVersion}.tar.gz" "http://${GetUrl}/${PhpVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$PhpVersion;
		echo "正在解压 ${PhpVersion}.tar.gz ...";
		tar -zxf $AMHDir/packages/$PhpVersion.tar.gz -C $AMHDir/packages/untar;
	
		cd $AMHDir/packages/untar/$PhpVersion;
		groupadd www;
		useradd -m -s /sbin/nologin -g www www;
		if [ "$InstallModel" == '1' ]; then
			./configure --prefix=/usr/local/php --enable-fpm --with-fpm-user=www --with-fpm-group=www --with-config-file-path=/etc --with-config-file-scan-dir=/etc/php.d --with-openssl=/usr/local/openssl --with-zlib=/usr/local/zlib  --with-curl --enable-ftp --with-gd --with-jpeg-dir --with-png-dir --with-freetype-dir --enable-gd-native-ttf --enable-mbstring --enable-zip --with-iconv=/usr/local/libiconv --with-mysql=/usr/local/mysql --without-pear $PHPDisable;
		fi;
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

		echo "[完成] ${PhpVersion} 安装完成.";
	else
		echo '[完成] PHP 已经安装.';
	fi;
}

function InstallNginx()
{
	# [dir] /usr/local/nginx
	echo "[${NginxVersion} 安装中] ************************************************** >>";
	if [ ! -d /usr/local/nginx ]; then
		Downloadfile "${NginxVersion}.tar.gz" "http://${GetUrl}/${NginxVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$NginxVersion;
		echo "正在解压 ${NginxVersion}.tar.gz ...";
		tar -zxf $AMHDir/packages/$NginxVersion.tar.gz -C $AMHDir/packages/untar;

		cd $AMHDir/packages/untar/$NginxVersion;
		./configure --prefix=/usr/local/nginx --user=www --group=www --with-openssl=../openssl-src --with-zlib=../zlib-src --with-pcre=../pcre-src --with-jemalloc=../jemalloc-src --with-ipv6 --with-http_spdy_module --with-http_ssl_module --with-http_realip_module --with-http_addition_module --with-http_image_filter_module --with-http_sub_module --with-http_dav_module --with-http_flv_module --with-http_mp4_module --with-http_gzip_static_module --with-http_gunzip_module --with-http_auth_request_module --with-http_concat_module --with-http_random_index_module --with-http_secure_link_module --with-http_degradation_module --with-http_sysguard_module --without-mail_pop3_module --without-mail_imap_module --without-mail_smtp_module --without-http_uwsgi_module --without-http_scgi_module;
		make -j $Cpunum;
		make install;

		mkdir -p /home/wwwroot/index /home/backup /usr/local/nginx/conf/vhost/  /usr/local/nginx/conf/vhost_stop/  /usr/local/nginx/conf/rewrite/;
		chown +w /home/wwwroot/index;
		touch /usr/local/nginx/conf/rewrite/amh.conf;

		cp $AMHDir/conf/nginx.conf /usr/local/nginx/conf/nginx.conf;
		cp $AMHDir/conf/nginx-host.conf /usr/local/nginx/conf/nginx-host.conf;
		cp $AMHDir/conf/fcgi.conf /usr/local/nginx/conf/fcgi.conf;
		cp $AMHDir/conf/fcgi-host.conf /usr/local/nginx/conf/fcgi-host.conf;
		cp $AMHDir/conf/nginx /root/amh/nginx;
		cp $AMHDir/conf/host /root/amh/host;
		chmod +x /root/amh/nginx;
		chmod +x /root/amh/host;
		sed -i 's/www.amysql.com/'$Domain'/g' /usr/local/nginx/conf/nginx.conf;

		cd /home/wwwroot/index;
		mkdir -p tmp etc/rsa bin usr/sbin log;
		touch etc/upgrade.conf;
		chown mysql:mysql etc/rsa;
		chmod 777 tmp;
		[ "$SysBit" == '64' ] && mkdir lib64 || mkdir lib;
		/usr/local/nginx/sbin/nginx;
		/usr/local/php/sbin/php-fpm;
		ln -s /usr/local/nginx/sbin/nginx /usr/bin/nginx;

		echo "[完成] ${NginxVersion} 安装完成.";
	else
		echo '[完成] Nginx 已经安装.';
	fi;
}

function InstallPureFTPd()
{
	# [dir] /etc/	/usr/local/bin	/usr/local/sbin
	echo "[${PureFTPdVersion} 安装中] ************************************************** >>";
	if [ ! -f /etc/pure-ftpd.conf ]; then
		Downloadfile "${PureFTPdVersion}.tar.gz" "http://${GetUrl}/${PureFTPdVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$PureFTPdVersion;
		echo "正在解压 ${PureFTPdVersion}.tar.gz ...";
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
		echo "[完成] ${PureFTPdVersion} 安装完成.";
	else
		echo '[完成] PureFTPd 已经安装.';
	fi;
}

function InstallAMH()
{
	# [dir] /home/wwwroot/index/web
	echo "[${AMHVersion} 安装中] ************************************************** >>";
	if [ ! -d /home/wwwroot/index/web ]; then
		Downloadfile "${AMHVersion}.tar.gz" "http://${GetUrl}/${AMHVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$AMHVersion;
		echo "正在解压 ${AMHVersion}.tar.gz ...";
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

		echo "[完成] ${AMHVersion} 安装完成.";
	else
		echo '[完成] AMH 已经安装.';
	fi;
}

function InstallAMS()
{
	# [dir] /home/wwwroot/index/web/ams
	echo "[${AMSVersion} 安装中] ************************************************** >>";
	if [ ! -d /home/wwwroot/index/web/ams ]; then
		Downloadfile "${AMSVersion}.tar.gz" "http://${GetUrl}/${AMSVersion}.tar.gz";
		rm -rf $AMHDir/packages/untar/$AMSVersion;
		echo "正在解压 ${AMSVersion}.tar.gz ...";
		tar -xf $AMHDir/packages/$AMSVersion.tar.gz -C $AMHDir/packages/untar;

		cp -r $AMHDir/packages/untar/$AMSVersion /home/wwwroot/index/web/ams;
		chown www:www -R /home/wwwroot/index/web/ams/View/DataFile;
		echo "[完成] ${AMSVersion} 安装完成.";
	else
		echo '[完成] AMS 已经安装.';
	fi;
}


# AMH Installing ****************************************************************************
CheckSystem;
ConfirmInstall;
InputDomain;
InputMysqlPass;
InputAMHPass;
Timezone;
CloseSelinux;
DeletePackages;
InstallBasePackages;
InstallReady;
InstallLibiconv;
InstallPcre;
InstallZlib;
InstallOpenssl;
InstallJemalloc;
InstallSafeSshd;
InstallMysql;
InstallPhp;
InstallNginx;
InstallPureFTPd;
InstallAMH;
InstallAMS;

if [ -s /usr/local/nginx ] && [ -s /usr/local/php ] && [ -s /usr/local/mysql ]; then

cp $AMHDir/conf/amh-start /etc/init.d/amh-start;
chmod 775 /etc/init.d/amh-start;
if [ "$SysName" == 'centos' ]; then
	chkconfig --add amh-start;
	chkconfig amh-start on;
else
	update-rc.d -f amh-start defaults;
fi;

/etc/init.d/amh-start;
rm -rf $AMHDir;

echo '================================================================';
	echo '恭喜你, AMH 4.2 安装完成.';
	echo "AMH 管理地址: http://${Domain}:8888";
	echo '用户名:admin';
	echo "密码:${AMHPass}";
	echo "MySQL 密码:${MysqlPass}";
	echo '';
	echo '******* SSH 管理命令 *******';
	echo '站点: amh host';
	echo 'PHP: amh php';
	echo 'Nginx: amh nginx';
	echo 'MySQL: amh mysql';
	echo 'FTP: amh ftp';
	echo '备份: amh backup';
	echo '还原: amh revert';
	echo '设置参数: amh SetParam';
	echo '模块 : amh module';
	echo '定时任务 : amh crontab';
	echo '升级 : amh upgrade';
	echo '信息: amh info';
	echo '';
	echo '******* SSH 管理目录 *******';
	echo '站点目录: /home/wwwroot';
	echo 'Nginx: /usr/local/nginx';
	echo 'PHP: /usr/local/php';
	echo 'MySQL: /usr/local/mysql';
	echo 'MySQL-Data: /usr/local/mysql/data';
	echo '';
	echo "起始时间: ${StartDate}";
	echo "完成时间: $(date) (Use: $[($(date +%s)-StartDateSecond)/60] minute)";
	echo '如需帮助请联系QQ 766464159 或访问 http://www.yuxiaoxi.com';
echo '================================================================';
else
	echo '抱歉, AMH 安装失败';
	echo '请联系QQ 766464159 或访问 http://www.yuxiaoxi.com';
fi;
