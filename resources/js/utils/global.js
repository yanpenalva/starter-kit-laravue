const useGlobal = () => {
  const versions = new Map([
    ['local', ' .0.0'],
    ['development', '3.0.0'],
    ['staging', '3.0.0'],
    ['production', '3.0.0'],
  ]);

  const env = import.meta.env.VITE_APP_ENV;
  const version = versions.get(env) ?? '0.0.0';

  return { appVersion: `v ${version}` };
};

export default useGlobal;
