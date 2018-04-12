# learnpress-interaction
Este cód. foi desenvolvido para fazer uma interação entre uma api do cliente com o plugin learnpress do Wordpress.
O cliente queria administrar as compras dos cursos através da ferramenta que já possuia mas fazer toda a parte das aulas através do wordpress.

Possui as seguintes funções:
  1. Widget que faz Login em um api externa
  2. É criado um usuário no wordpress com as credenciais retornadas
  3. É verificado na api do usuário os cursos que foram adiquiridos
  4. A partir dos cursos adquiridos é feita uma varredura nos cursos cadastrados no wp, e os cursos que possuem registros são adicionados ao usuário, como se ele os tivesse comprado.
