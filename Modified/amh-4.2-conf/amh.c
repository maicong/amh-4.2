/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param amh shell系统入口
 * Update:2013-11-01
 * 
 ************************************************/
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/types.h>
#include <unistd.h>

int main(int argc, char * argv[])
{
		int i, k, r = 0;
        FILE * fp;
        uid_t amh_uid, amh_euid;
		char amh_uid_str[10] = {0};
		char amh_cmd_all[2048] = "export amh_uid=";
		char amh_cmd[2048] = {0};
		char amh_cmd_base[][20] = {"host", "nginx", "php", "mysql", "ftp", "BRftp", "BRssh", "backup", "revert", "crontab", "SetParam", "module", "upgrade"};
		char amh_cmd_base_val[][50] = {
			"/root/amh/host ",
			"/root/amh/nginx ",
			"/root/amh/php ",
			"/root/amh/mysql ",
			"/root/amh/ftp ",
			"/root/amh/BRftp ",
			"/root/amh/BRssh ",
			"/root/amh/backup ",
			"/root/amh/revert ",
			"/root/amh/crontab ",
			"/root/amh/SetParam ",
			"/root/amh/module ",
			"/root/amh/upgrade "			
		};
		char amh_cmd_quick[][20] = {"info", "ls_rewrite", "ls_ftp", "ls_wwwroot", "ls_vhost", "ls_vhost_stop", "ls_modules", "ls_backup", "cat_php_ini", "cat_my_cnf", "cat_nginx"};
		char amh_cmd_quick_val[][80] = {
			"/root/amh/info",
			"ls /usr/local/nginx/conf/rewrite",
			"cat /etc/pureftpd.passwd",
			"ls /home/wwwroot",
			"ls /usr/local/nginx/conf/vhost",
			"ls /usr/local/nginx/conf/vhost_stop",
			"ls /root/amh/modules",
			"ls -l /home/backup",
			"cat /etc/php.ini",
			"cat /etc/my.cnf",
			"cat /usr/local/nginx/conf/nginx.conf"
		};
		char rm_backup[] = "rm -f /home/backup/";
		char cat_vhost[] = "cat /usr/local/nginx/conf/vhost/";
		char cat_vhost_stop[] = "cat /usr/local/nginx/conf/vhost_stop/";
		char cat_php_pid[] = "cat /usr/local/php/var/run/pid/";
		char cat_php_fpm[] = "cat /usr/local/php/etc/fpm/";

		for (k = 1; k < argc; k++)
		{
			i = 0;
			char cmd_row[2048];
			strcpy(cmd_row, argv[k]);
			int cmd_row_len = (int)strlen(cmd_row);
			for (; i < cmd_row_len; i++)
				if (cmd_row[i] == ' ' || cmd_row[i] == ';' || cmd_row[i] == '&' || cmd_row[i] == '|') cmd_row[i] = '_';
			strcpy(argv[k], cmd_row);
		}

		int _break = 0, _continue = 0, _amh_cmd_sum = 0;
        for (k = 1; k < argc; k++)
        {
			if(k == 1)
			{
				_amh_cmd_sum = sizeof(amh_cmd_base) / sizeof(amh_cmd_base[0]);
				for (i = 0; i < _amh_cmd_sum; i++)
				{
					if(strcmp(argv[k], amh_cmd_base[i]) == 0)
					{
						strcat(amh_cmd, amh_cmd_base_val[i]);
						_continue = 1;
						break;
					}
				}
				if(_continue) continue;

				_amh_cmd_sum = sizeof(amh_cmd_quick) / sizeof(amh_cmd_quick[0]);
				for (i = 0; i < _amh_cmd_sum; i++)
				{
					if(strcmp(argv[k], amh_cmd_quick[i]) == 0)
					{
						strcat(amh_cmd, amh_cmd_quick_val[i]);
						_break = 1;
						break;
					}
				}
				if(_break) break;

				if (strcmp(argv[k], "rm_backup") == 0)
				{
					strcat(amh_cmd, rm_backup);
					strcat(amh_cmd, argv[2]);
					strcat(amh_cmd, ".amh");
					break;
				}
				else if (strcmp(argv[k], "cat_vhost") == 0)
				{
					strcat(amh_cmd, cat_vhost);
					strcat(amh_cmd, argv[2]);
					strcat(amh_cmd, ".conf");
					break;
				}
				else if (strcmp(argv[k], "cat_vhost_stop") == 0)
				{
					strcat(amh_cmd, cat_vhost_stop);
					strcat(amh_cmd, argv[2]);
					strcat(amh_cmd, ".conf");
					break;
				}
				else if (strcmp(argv[k], "cat_php_pid") == 0)
				{
					strcat(amh_cmd, cat_php_pid);
					strcat(amh_cmd, argv[2]);
					strcat(amh_cmd, ".pid");
					break;
				}
				else if (strcmp(argv[k], "cat_php_fpm") == 0)
				{
					strcat(amh_cmd, cat_php_fpm);
					strcat(amh_cmd, argv[2]);
					strcat(amh_cmd, ".conf");
					break;
				}
				exit(0);
			}
			else 
			{
				strcat(amh_cmd, argv[k]);
				strcat(amh_cmd, " ");
			}
        }
		if (argc == 1) strcat(amh_cmd, amh_cmd_quick_val[0]);

		amh_uid = getuid();
		amh_euid = geteuid();
		sprintf(amh_uid_str, "%d", amh_uid);
		strcat(amh_cmd_all, amh_uid_str);
		strcat(amh_cmd_all, " && ");
		strcat(amh_cmd, "; echo $? >/tmp/amh.result");
		strcat(amh_cmd_all, amh_cmd);
		setreuid(amh_euid, amh_euid);
        system(amh_cmd_all);
        fp = fopen("/tmp/amh.result", "r");
        if (fp)
        {
			fscanf(fp, "%d", &r);
			fclose(fp);
        }
        return r;
}
