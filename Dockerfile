# =============================================================================
# CentOS-7, Apache 2.2, PHP 5.6
# ORIGINAL: https://github.com/naqoda/docker-centos-apache-php/blob/master/Dockerfile
# =============================================================================
FROM centos:centos7

MAINTAINER DIRTI_CGDES_Arquitetura <DIRTI_CGDES_Arquitetura@fnde.gov.br>

ARG uid=1000

# -----------------------------------------------------------------------------
# Import the RPM GPG keys for Repositories
# -----------------------------------------------------------------------------
RUN rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
	&& rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm

# -----------------------------------------------------------------------------
# Apache + PHP
# -----------------------------------------------------------------------------
RUN	yum -y update \
        && yum --setopt=tsflags=nodocs -y install \
        httpd \
        gcc \
        gcc-c++ \
        mod_ssl \
        php56w \
		php56w-common \
        php56w-devel \
        php56w-cli \
        php56w-devel \
        php56w-pdo \
		php56w-pgsql \
        php56w-pgsql \
        php56w-mysql \
		php56w-mssql \
        php56w-mbstring \
        php56w-soap \
        php56w-gd \
        php56w-xml \
		libaio \
        unzip
		
RUN 	rm -rf /var/cache/yum/* \
        && yum clean all

# -----------------------------------------------------------------------------
# UTC Timezone & Networking
# -----------------------------------------------------------------------------
RUN ln -sf /usr/share/zoneinfo/UTC /etc/localtime \
	&& echo "NETWORKING=yes" > /etc/sysconfig/network

# -----------------------------------------------------------------------------
# Global PHP configuration changes
# -----------------------------------------------------------------------------

RUN sed -i "s/log_errors = Off/log_errors = On/" /etc/php.ini
#RUN sed -i "s/error_log = /dev/stderr" /etc/php.ini
#RUN sed -i "s/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING/" /etc/php.ini
RUN sed -i "s/display_startup_errors = Off/display_startup_errors = On/" /etc/php.ini 
RUN sed -i "s/display_errors = Off/display_errors = On/" /etc/php.ini

 
	
	
# -----------------------------------------------------------------------------
# Configurando inicializacao automatica do apache
# -----------------------------------------------------------------------------	
RUN systemctl enable httpd.service


# -----------------------------------------------------------------------------
# INSTALL ORACLE PDO OCI and OCI8
# -----------------------------------------------------------------------------
RUN mkdir -p /usr/lib/oracle/11.2/client64/lib/
ADD oracle/libclntsh.so.11.1.gz /usr/lib/oracle/11.2/client64/lib/
RUN gunzip /usr/lib/oracle/11.2/client64/lib/libclntsh.so.11.1.gz
ADD oracle/libnnz11.so.gz /usr/lib/oracle/11.2/client64/lib/
RUN gunzip /usr/lib/oracle/11.2/client64/lib/libnnz11.so.gz
ADD oracle/libocci.so.11.1.gz /usr/lib/oracle/11.2/client64/lib/
RUN gunzip /usr/lib/oracle/11.2/client64/lib/libocci.so.11.1.gz
RUN ln -s /usr/lib/oracle/11.2/client64/lib/libclntsh.so.11.1 /usr/lib/oracle/11.2/client64/lib/libclntsh.so
RUN ln -s /usr/lib/oracle/11.2/client64/lib/libocci.so.11.1 /usr/lib/oracle/11.2/client64/lib/libocci.so
RUN echo "/usr/lib/oracle/11.2/client64/lib" > /etc/ld.so.conf.d/oracle.conf
RUN ldconfig

ADD oracle/oci8.so /usr/lib64/php/modules/
RUN echo "extension=oci8.so" > /etc/php.d/oci8.ini

ADD oracle/pdo_oci.so /usr/lib64/php/modules/
RUN echo "extension=pdo_oci.so" > /etc/php.d/pdo_oci.ini


# -----------------------------------------------------------------------------
# Remove packages and files
# -----------------------------------------------------------------------------
RUN yum -y remove \
	gcc \
	gcc-c++ \
	&& rm -rf /var/cache/yum/* \
	&& yum clean all \
	&& rm -Rf /home/oracle/src/*


# -----------------------------------------------------------------------------
# Set default environment variables used to configure the service container
# -----------------------------------------------------------------------------
	ENV APACHE_SERVER_ALIAS "" 
	ENV APACHE_SERVER_NAME SRV-PHP56 
	ENV APP_HOME_DIR /var/www/html
	ENV DATE_TIMEZONE UTC

	ENV ORACLE_HOME "/usr/lib/oracle/12.1/client64"
	ENV LD_LIBRARY_PATH "/usr/lib/oracle/12.1/client64/lib"
	ENV NLS_LANG=AMERICAN_AMERICA.WE8ISO8859P1

# -----------------------------------------------------------------------------
# Set ports
# -----------------------------------------------------------------------------
EXPOSE 80 443

# -----------------------------------------------------------------------------
# Copy Alias files
# -----------------------------------------------------------------------------
ADD conf.d/aplicacao_sice.conf /etc/httpd/conf.d/
ADD conf.d/aplicacao_static.conf  /etc/httpd/conf.d/


# -----------------------------------------------------------------------------
# Create volume folder
# -----------------------------------------------------------------------------
RUN mkdir /var/www/sice
RUN mkdir /var/www/zend
RUN mkdir /var/www/static
ADD index.php /var/www/html/

CMD ["/usr/sbin/httpd", "-D", "FOREGROUND"]


