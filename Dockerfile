FROM iamjothees/laravel-image:php8.3

ENV NODE_VERSION 22

RUN source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

EXPOSE 4173