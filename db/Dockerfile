FROM postgres:15

# Set default environment variables (can be overridden by docker-compose)
ENV POSTGRES_DB=mydb
ENV POSTGRES_USER=myuser
ENV POSTGRES_PASSWORD=mypassword

# Copy initialization scripts
COPY ./db-init /docker-entrypoint-initdb.d/

# Expose PostgreSQL port
EXPOSE 5432
