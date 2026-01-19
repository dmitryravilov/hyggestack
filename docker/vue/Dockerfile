FROM node:20-alpine AS base

# Install dumb-init for proper signal handling and wget for healthcheck
RUN apk add --no-cache dumb-init wget

# Create app directory
WORKDIR /app

# Note: node user (UID 1000) already exists in node:20-alpine image

# Copy package files (as root)
COPY --chown=node:node frontend/package*.json ./

# Install dependencies as root, then change ownership
# Try npm ci first (for reproducible builds), fall back to npm install if lock file is out of sync
RUN (npm ci || npm install) && \
    npm cache clean --force

# Copy themes directory to /themes (needed for Vite build)
# The @themes alias in vite.config.js resolves to ../themes from /app, which is /themes
COPY --chown=node:node themes /themes

# Copy frontend application files
COPY --chown=node:node frontend/ .

# Ensure all files are owned by node user (including node_modules)
RUN chown -R node:node /app /themes

# Switch to non-root user
USER node

# Expose Vite dev server port
EXPOSE 5173

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD wget --no-verbose --tries=1 --spider http://localhost:5173 || exit 1

# Use dumb-init to handle signals properly
ENTRYPOINT ["dumb-init", "--"]
CMD ["npm", "run", "dev", "--", "--host"]


