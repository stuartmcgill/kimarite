<script setup lang="ts"></script>

<template>
  <footer
    class="mt-auto p-4 flex flex-col sm:flex-row items-center justify-between gap-2"
  >
    <div class="flex text-lg">
      Data from
      <a
        class="ml-1 flex items-center hover:opacity-70"
        href="https://www.sumo-api.com/"
        title="Sumo API"
        target="_blank"
        >Sumo API</a
      >
    </div>
    <div class="flex items-center">
      <img src="favicon.ico" class="mx-1 h-4 w-4" />
      Dohyo icon by
      <a
        class="ml-1 flex items-center hover:opacity-70"
        href="https://www.flaticon.com/authors/vitaly-gorbachev"
        title="Vitaly Gorbachev"
        >Vitaly Gorbachev - Flaticon</a
      >
    </div>
    <a
      class="hover:opacity-70"
      href="https://github.com/stuartmcgill/kimarite"
      title="Github repository"
    >
      <i class="pi pi-github" style="font-size: 1.5rem" />
    </a>
  </footer>
</template>
