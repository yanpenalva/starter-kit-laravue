const useGlobal = () => {
  const versions = new Map([
    ['local', '2.0.0'],
    ['development', '2.0.0'],
    ['staging', '2.0.0'],
    ['production', '2.0.0'],
  ]);

  const env = import.meta.env.VITE_APP_ENV;
  const version = versions.get(env) ?? '0.0.0';

  return { appVersion: `v ${version}` };
};

export default useGlobal;
