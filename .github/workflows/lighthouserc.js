module.exports = {
  ci: {
    url: ["http://localhost/", "http://localhost/demo,", "http://127.0.0.1:8080", "http://127.0.0.1:8080/demo"],
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
