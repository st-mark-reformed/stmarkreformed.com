This project is a monorepo.

- The old Craft CMS app (which is being migrated away from) lives in the `craft-cms` directory
- The new front-end app, which is Next JS, lives in the `web` directory
- Theres a new PHP back-end/api app that lives in the `api` directory.
- The `docker` directory contains local dev and production (swarm) Docker configuration
- The `.github` directory contains the github CI workflows that build and deploy the app
- This app has local dev scripting through the `dev` file. The primary app for the dev scripting lives in `devCliSrc`

Under most circumstances, you will not need to modify or create files in the root of this monorepo, but will be working in the sub-project applications.
