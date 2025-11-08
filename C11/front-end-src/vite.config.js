import { defineConfig } from "vite";

export default defineConfig({
  server: {
    port: 3000,
  },
  build: {
    outDir: "../front-end",
    emptyOutDir: true,
  },
});
