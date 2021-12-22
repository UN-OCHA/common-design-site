module.exports = {
  ci: {
    collect: {
      url: ['http://127.0.0.1:8080'],
      startServerCommand: '../vendor/bin/drush rs 127.0.0.1:8080 >/dev/null 2>&1 &',
    },
    upload: {
      target: 'temporary-public-storage',
    },
  },
};
