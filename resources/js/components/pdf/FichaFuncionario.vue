<script setup lang="ts">
import { Document, Page, Text, View } from "@ceereals/vue-pdf";
import { reactive } from "vue";
const viewStyle = reactive({
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center",
    height: "100%",
});

const textStyle = reactive({
    fontSize: 24,
});
</script>
<template>
    <Document>
        <Page size="A4">
            <View :style="viewStyle">
                <Text :style="textStyle">Hello, Vue PDF!</Text>
            </View>
        </Page>
    </Document>
</template>
