
<template>

  <div class='topMenu' id='topMenu' > 

    <!-- itens do menu -->
    <div v-for='item in props.menuItens' :id="'itemMenu' + item.id" class='itemMenu' :class='{itemMenuSelected: props.selectedMenuItem==item.id}'  :key='item.id' 
        :style="{ 
          backgroundImage: `url(src/assets/images/${item.icone}`,   
          backgroundRepeat: 'no-repeat',
          backgroundPositionY: 'center'  ,
          backgroundPositionX: '10px',

        }"
        @click="emit('setMenuItem', item.id)"   >
        <span>{{item.titulo}}</span>
    </div>

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
  setTimeout(() => {
  emit('hideLoading')  
  }, 1000);

})
    


</script>

