FROM php:7.3-apache

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update
RUN apt-get -y install python3
RUN apt-get -y install python3-pip
RUN apt-get -y install git
RUN cd /opt
RUN git clone -b mysql https://github.com/os2kitos/kitos_tools.git /opt/kitos_tools
#RUN git checkout mysql
RUN pip3 install -r /opt/kitos_tools/requirements.txt
RUN pip3 install -r /opt/kitos_tools/exporters/requirements.txt
RUN pip3 install /opt/kitos_tools/kitos_tools

# RUN mv /opt/kitos_tools/settings/settings_example.json /opt/kitos_tools/settings/settings.json
RUN (cd /opt/kitos_tools/settings/ && mv settings_example.json settings.json)

#RUN mkdir -p /var/www/html/uploads
#RUN chown -R www-data:www-data /var/www/html/uploads/
# For at få python til at køre i 
RUN chown -R www-data:www-data /opt/kitos_tools/

COPY ./run_first.sh /
COPY ./change_set_rights.py /
RUN chmod a+x /run_first.sh
RUN /run_first.sh
#ENTRYPOINT ["/run_first.sh"]
