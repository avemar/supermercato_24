module.exports = {
    verbose: true,
    testMatch: ['**/?(*.)test.js'],
    collectCoverage: false,
    collectCoverageFrom: ['src/**/app/*.js'],
    coverageThreshold: {
        global: {
            branches: 100,
            functions: 100,
            lines: 100,
            statements: 100,
        },
    },
    notify: true,
    cacheDirectory: './coverage/cache',
};
