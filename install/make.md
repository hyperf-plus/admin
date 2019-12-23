  export LDFLAGS="-L/usr/local/opt/openssl/lib"
  export CPPFLAGS="-I/usr/local/opt/openssl/include"
  export PKG_CONFIG_PATH="/usr/local/opt/openssl/lib/pkgconfig"


  export LDFLAGS="-L/usr/local/opt/libiconv/lib"
  export CPPFLAGS="-I/usr/local/opt/libiconv/include"
  export CPPFLAGS="-I/usr/local/opt/libxml2/include"


ln -s /usr/local/opt/libxml2/include/libxml2/libxml /usr/include/libxml2


/bin/sh /Users/maozihao/Desktop/dev/gov/php-7.4.0/libtool --silent --preserve-dup-deps --mode=compile cc -DZEND_ENABLE_STATIC_TSRMLS_CACHE=1 -Iext/libxml/ -I/Users/maozihao/Desktop/dev/gov/php-7.4.0/ext/libxml/ -DPHP_ATOM_INC -I/Users/maozihao/Desktop/dev/gov/php-7.4.0/include -I/Users/maozihao/Desktop/dev/gov/php-7.4.0/main -I/Users/maozihao/Desktop/dev/gov/php-7.4.0 -I/Users/maozihao/Desktop/dev/gov/php-7.4.0/ext/date/lib -I/usr/local/opt/libxml2/include/libxml2 -I/usr/local/Cellar/openssl/1.0.2s/include -I/usr/local/opt/libiconv//include -I/usr/local/Cellar/oniguruma/6.9.2/include -I/Users/maozihao/Desktop/dev/gov/php-7.4.0/ext/mbstring/libmbfl -I/Users/maozihao/Desktop/dev/gov/php-7.4.0/ext/mbstring/libmbfl/mbfl -I/Users/maozihao/Desktop/dev/gov/php-7.4.0/TSRM -I/Users/maozihao/Desktop/dev/gov/php-7.4.0/Zend  -I/usr/local/opt/libxml2/include -no-cpp-precomp -pthread  -I/usr/local/opt/libiconv//include -g -O2 -fvisibility=hidden -pthread -Wall -Wno-strict-aliasing -DZTS -DZEND_SIGNALS   -c /Users/maozihao/Desktop/dev/gov/php-7.4.0/ext/libxml/libxml.c -o ext/libxml/libxml.lo

./configure --prefix=/usr/local/php/7.4 \
--with-config-file-path=/usr/local/php/7.4/etc \
--with-config-file-scan-dir=/usr/local/php/7.4/etc/conf.d \
--enable-redis \
--with-mysqli \
--with-pdo-mysql \
--with-iconv=/usr/local/opt/libiconv/ \
--enable-short-tags \
--with-freetype-dir \
--with-zlib \
--with-jpeg-dir \
--with-png-dir \
--with-libxml-dir=/usr/bin/xml2-config \
--enable-xml \
--with-xml-include=/usr/local/opt/libxml2/include/libxml2 \
--with-xml2-include=/usr/local/opt/libxml2/include/libxml2 \
--disable-rpath \
--enable-bcmath \
--enable-shmop \
--enable-sysvsem \
--enable-inline-optimization \
--with-curl \
--enable-mbregex \
--enable-mbstring \
--with-mcrypt \
--with-gd \
--enable-gd-native-ttf \
--with-openssl=/usr/local/opt/openssl \
--with-mhash \
--enable-pcntl \
--enable-sockets \
--with-xmlrpc \
--enable-zip \
--enable-soap \
--without-pear \
--enable-fileinfo \
--enable-maintainer-zts \
--enable-mysqlnd




 ./configure --with-php-config=/usr/local/php/7.4/bin/php-config  --enable-mysqlnd  --enable-mysqlnd --enable-openssl --with-openssl-dir=/usr/local/opt/openssl 
 
 
 备用
 

ALTER TABLE `new_admin`.`cs_user` 
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 0 COMMENT '租户ID' ;

ALTER TABLE `new_admin`.`cs_auth_group` 
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 0 COMMENT '租户ID' ;


ALTER TABLE `new_admin`.`cs_article_cat` 
ADD COLUMN `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后更新时间' ;

ALTER TABLE `new_admin`.`cs_article_cat` 
ADD COLUMN `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后更新时间' ;

ALTER TABLE `new_admin`.`cs_action_log` 
 ADD COLUMN `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后更新时间' ;
                                                                      
  ALTER TABLE `new_admin`.`cs_action_log` 
 ADD COLUMN `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后更新时间' ;


ALTER TABLE `new_admin`.`cs_message_user` 
 ADD COLUMN `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后更新时间' ;

