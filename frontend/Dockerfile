FROM node:22

WORKDIR /app

COPY package*.json ./

RUN npm config set fetch-retry-maxtimeout 120000 && \
    npm config set fetch-retries 5 && \
    npm install --legacy-peer-deps

COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]
