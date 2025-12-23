<script setup>
import { computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  items: { type: Array, default: () => [] },
})

const SCRIPT_ID = 'km-jsonld-breadcrumbs'

function toAbsoluteUrl(href) {
  if (!href) return null
  try {
    if (/^https?:\/\//i.test(href)) return href
    return new URL(href, window.location.origin).toString()
  } catch { return null }
}

const jsonLd = computed(() => {
  const items = Array.isArray(props.items) ? props.items : []
  if (!items.length) return ''
  const list = items.map((it, idx) => {
    const name = String(it?.label ?? '').trim() || `Item ${idx + 1}`
    const href = it?.href ?? it?.url ?? null
    const abs = toAbsoluteUrl(href)
    const obj = { '@type':'ListItem', position: idx + 1, name }
    if (abs) obj.item = abs
    return obj
  })
  return JSON.stringify({ '@context':'https://schema.org', '@type':'BreadcrumbList', itemListElement:list })
})

function upsertJsonLdScript() {
  if (typeof document === 'undefined') return
  const content = jsonLd.value
  if (!content) { const ex=document.getElementById(SCRIPT_ID); if(ex) ex.remove(); return }
  let el = document.getElementById(SCRIPT_ID)
  if (!el) { el=document.createElement('script'); el.id=SCRIPT_ID; el.type='application/ld+json'; document.head.appendChild(el) }
  el.textContent = content
}

function removeJsonLdScript() {
  if (typeof document === 'undefined') return
  const el = document.getElementById(SCRIPT_ID)
  if (el) el.remove()
}

onMounted(() => upsertJsonLdScript())
watch(() => props.items, () => upsertJsonLdScript(), { deep: true })
onBeforeUnmount(() => removeJsonLdScript())
</script>

<template>
  <nav v-if="items && items.length" aria-label="Breadcrumb" class="mb-6 text-sm text-slate-700">
    <ol class="flex flex-wrap items-center gap-2">
      <li v-for="(item, index) in items" :key="`${index}-${item?.label ?? ''}-${item?.href ?? item?.url ?? ''}`" class="flex items-center gap-2">
        <template v-if="(item?.href || item?.url) && index !== items.length - 1">
          <Link :href="item.href || item.url" class="hover:text-slate-700 underline-offset-2 hover:underline">
            {{ item.label }}
          </Link>
        </template>
        <template v-else>
          <span class="text-slate-700 font-medium">{{ item?.label }}</span>
        </template>
        <span v-if="index !== items.length - 1" class="text-slate-600">/</span>
      </li>
    </ol>
  </nav>
</template>
