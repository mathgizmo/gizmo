// The file contents for the current environment will overwrite these during build.
// The build system defaults to the dev environment which uses `environment.ts`, but if you do
// `ng build --env=prod` then `environment.prod.ts` will be used instead.
// The list of which env maps to which file can be found in `.angular-cli.json`.

export const environment = {
  production: false,
  apiUrl: 'your_local_server_url/api', // your local API URL here
  adminUrl: 'your_local_server_url/admin',
  baseUrl: window.location.origin + '/gizmo', // Base URL here
  captchaKey: '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
  ignoreCaptcha: false
};
