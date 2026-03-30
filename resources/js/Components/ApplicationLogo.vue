<template>
    <img src="/images/logo.png" alt="TM7 Club Logo" v-bind="$attrs" @error="$event.target.style.display='none'" />
</template>
