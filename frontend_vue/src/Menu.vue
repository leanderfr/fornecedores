
<template>

  <div class='topMenu' id='topMenu' > 

    <!-- itens do menu -->
    <template v-if='menuItens.length!=0' >
        <div v-for='item in menuItens' :id="'itemMenu' + item.id" class='itemMenu' :class='{itemMenuSelected: props.selectedMenuItem==item.id}'  :key='item.id' 
            :style="{ 
              backgroundImage: `url('./assets/images/${item.icone}.png')` ,
              backgroundRepeat: 'no-repeat',
              backgroundSize: '140px 90px',
              backgroundPositionY: 'center'

            }" 
            @click="emit('setMenuItem', item.id)"   >
            <span>{{item.titulo}}</span>
        </div>
      </template>

    </div>

</template>


<script setup>
import { onMounted  } from 'vue';
const props = defineProps( ['selectedMenuItem', 'menuItens'] )

// usuario escolheu um item do menu
const emit = defineEmits(['setMenuItem', 'hideLoading']);


//*****************************************************************************
//*****************************************************************************
onMounted( () => {
console.log('cheou')
emit('hideLoading')
console.log('cheou222')

})
    


</script>

