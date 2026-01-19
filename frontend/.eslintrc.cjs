/* eslint-env node */
module.exports = {
  root: true,
  env: {
    browser: true,
    es2021: true,
    node: true,
  },
  extends: [
    'standard',
    'plugin:vue/vue3-recommended',
    'plugin:vue/vue3-strongly-recommended',
  ],
  parserOptions: {
    ecmaVersion: 2022,
    sourceType: 'module',
  },
  plugins: ['vue'],
  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'warn',
    'vue/multi-word-component-names': 'off',
    'vue/no-v-html': 'warn',
    'vue/require-default-prop': 'off',
    'vue/require-explicit-emits': 'error',
    'vue/component-name-in-template-casing': ['error', 'PascalCase'],
    'vue/define-macros-order': ['error', {
      order: ['defineOptions', 'defineProps', 'defineEmits'],
    }],
    'vue/define-props-declaration': ['error', 'type-based'],
    'vue/no-unused-vars': 'error',
    'vue/no-unused-components': 'warn',
    'vue/order-in-components': 'error',
    'vue/this-in-template': 'error',
    'comma-dangle': ['error', 'always-multiline'],
    'space-before-function-paren': ['error', {
      anonymous: 'always',
      named: 'never',
      asyncArrow: 'always',
    }],
  },
}

