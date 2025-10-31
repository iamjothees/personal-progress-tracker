FROM iamjothees/laravel-image:php8.3

ENV NODE_VERSION=22

RUN source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

RUN echo 'alias nrd="npm run dev -- --host"'  >> ~/.bashrc

CMD nvm use $NODE_VERSION

EXPOSE 4173