FROM kitpages/symfony:7.0

RUN apt-get update &&\
    DEBIAN_FRONTEND=noninteractive apt-get install -y git php7.0-sqlite3 \
    php7.0-xml &&\
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
