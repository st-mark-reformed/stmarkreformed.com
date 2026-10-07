FROM node:22-slim AS base
ENV NODE_ENV=production
ENV PNPM_HOME="/pnpm"
ENV PATH="$PNPM_HOME:$PATH"
ENV COREPACK_ENABLE_DOWNLOAD_PROMPT=0
RUN npm install -g corepack@latest && corepack enable

######################
# Dependency installer
######################
FROM base AS deps
WORKDIR /app
COPY web/package.json web/pnpm-lock.yaml ./
RUN --mount=type=cache,id=pnpm,target=/pnpm/store pnpm install --frozen-lockfile


#####################
# Source code builder
#####################
FROM base AS builder
WORKDIR /app
COPY ./web .
COPY --from=deps /app/node_modules ./node_modules
RUN pnpm build


####################
# Application runner
####################
FROM base AS runner

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=builder /app/.next/standalone ./
COPY --from=builder /app/.next/static ./.next/static
COPY --from=builder /app/public ./public

EXPOSE 80

ENV PORT=80

# Next.js collects completely anonymous telemetry data about general usage.
# Learn more here: https://nextjs.org/telemetry
# Uncomment the following line in case you want to disable telemetry.
ENV NEXT_TELEMETRY_DISABLED=1

# Update bash profile
COPY ./docker/.profile /root/.profile
COPY ./docker/.profile /root/.bashrc

HEALTHCHECK --interval=5s --timeout=5s --start-period=10s --retries=10 CMD curl --fail http://localhost/healthcheck/659f0fa1b69e5 || exit 1

# The standalone server listens on $HOSTNAME, and the compose files set that to
# the container name, so force it to listen on all interfaces.
CMD ["env", "HOSTNAME=0.0.0.0", "node", "server.js"]
