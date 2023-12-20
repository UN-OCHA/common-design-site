module.exports = {
  ci: {
    collect: {
      url: [
        'http://127.0.0.1:8080',
        'http://127.0.0.1:8080/demo',
        'http://127.0.0.1:8080/layouts/sidebar-first'
      ],
      startServerCommand: 'docker compose -f tests/docker-compose.yml exec -T drupal drush rs 127.0.0.1:8080'
    },
    assert: {
      preset: "lighthouse:recommended",
      assertions: {
        "first-contentful-paint": ["error", {"minScore": 0.6}]
      }
    },
    upload: {
      target: "temporary-public-storage",
    },
  },
};
