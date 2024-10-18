FROM mongo:7.0.14

# Set environment variables (optional)
# Example: set root user and password for MongoDB
ENV MONGO_INITDB_ROOT_USERNAME=my_mongo_username
ENV MONGO_INITDB_ROOT_PASSWORD=my_mongo_password

# Copy custom MongoDB configuration file (if any) into the container
# Uncomment and modify if you have a custom config file
# COPY mongod.conf /etc/mongo/mongod.conf

# Set up a volume for data persistence
# VOLUME /data/db

# Expose the default MongoDB port
EXPOSE 27017

# Optionally, initialize some data or run a script at the start
# COPY ./init-db.sh /docker-entrypoint-initdb.d/

# Start the MongoDB service
CMD ["mongod"]