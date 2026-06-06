<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\Producto;
use App\Models\Punto;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Admin Diego\'s Pizza',
            'email' => 'admin@diegospizza.com',
            'password' => bcrypt('admin123'),
        ]);

        // Categorias
        $pizzas = Categoria::create(['nombre' => 'Pizzas', 'slug' => 'pizzas', 'descripcion' => 'Pizzas artesanales horneadas al momento', 'orden' => 1]);
        $especiales = Categoria::create(['nombre' => 'Especiales', 'slug' => 'especiales', 'descripcion' => 'Pizzas especiales de la casa', 'orden' => 2]);
        $bebidas = Categoria::create(['nombre' => 'Bebidas', 'slug' => 'bebidas', 'descripcion' => 'Refrescos y aguas', 'orden' => 3]);
        $postres = Categoria::create(['nombre' => 'Postres', 'slug' => 'postres', 'descripcion' => 'Postres caseros', 'orden' => 4]);
        $extras = Categoria::create(['nombre' => 'Extras', 'slug' => 'extras', 'descripcion' => 'Complementos para tu pizza', 'orden' => 5]);

        // Productos - Pizzas
        Producto::create(['categoria_id' => $pizzas->id, 'nombre' => 'Pizza Margherita', 'slug' => 'pizza-margherita', 'descripcion' => 'Salsa de tomate, mozzarella fresca y albahaca', 'precio' => 99.00, 'ingredientes' => 'Salsa de tomate, Mozzarella, Albahaca']);
        Producto::create(['categoria_id' => $pizzas->id, 'nombre' => 'Pizza Pepperoni', 'slug' => 'pizza-pepperoni', 'descripcion' => 'Salsa de tomate, mozzarella y pepperoni', 'precio' => 109.00, 'ingredientes' => 'Salsa de tomate, Mozzarella, Pepperoni']);
        Producto::create(['categoria_id' => $pizzas->id, 'nombre' => 'Pizza Hawiana', 'slug' => 'pizza-hawiana', 'descripcion' => 'Salsa de tomate, mozzarella, jamón y piña', 'precio' => 109.00, 'ingredientes' => 'Salsa de tomate, Mozzarella, Jamón, Piña']);
        Producto::create(['categoria_id' => $pizzas->id, 'nombre' => 'Pizza 4 Quesos', 'slug' => 'pizza-4-quesos', 'descripcion' => 'Mozzarella, parmesano, gorgonzola y queso de cabra', 'precio' => 119.00, 'ingredientes' => 'Mozzarella, Parmesano, Gorgonzola, Queso de cabra']);
        Producto::create(['categoria_id' => $pizzas->id, 'nombre' => 'Pizza Vegetariana', 'slug' => 'pizza-vegetariana', 'descripcion' => 'Salsa de tomate, mozzarella, champiñones, pimientos y aceitunas', 'precio' => 115.00, 'ingredientes' => 'Salsa de tomate, Mozzarella, Champiñones, Pimientos, Aceitunas']);

        // Productos - Especiales
        Producto::create(['categoria_id' => $especiales->id, 'nombre' => 'Pizza Carnívora', 'slug' => 'pizza-carnivora', 'descripcion' => 'Pepperoni, jamón, salchicha, carne molida y tocino', 'precio' => 139.00, 'ingredientes' => 'Pepperoni, Jamón, Salchicha, Carne molida, Tocino']);
        Producto::create(['categoria_id' => $especiales->id, 'nombre' => 'Pizza BBQ', 'slug' => 'pizza-bbq', 'descripcion' => 'Salsa BBQ, pollo, cebolla morada y mozzarella', 'precio' => 129.00, 'ingredientes' => 'Salsa BBQ, Pollo, Cebolla morada, Mozzarella']);
        Producto::create(['categoria_id' => $especiales->id, 'nombre' => 'Pizza Suprema', 'slug' => 'pizza-suprema', 'descripcion' => 'Pepperoni, jamón, champiñones, pimientos, cebolla y aceitunas', 'precio' => 149.00, 'ingredientes' => 'Pepperoni, Jamón, Champiñones, Pimientos, Cebolla, Aceitunas']);

        // Productos - Bebidas
        Producto::create(['categoria_id' => $bebidas->id, 'nombre' => 'Coca-Cola 600ml', 'slug' => 'coca-cola-600ml', 'descripcion' => 'Coca-Cola clásica 600 ml', 'precio' => 25.00]);
        Producto::create(['categoria_id' => $bebidas->id, 'nombre' => 'Sprite 600ml', 'slug' => 'sprite-600ml', 'descripcion' => 'Sprite 600 ml', 'precio' => 25.00]);
        Producto::create(['categoria_id' => $bebidas->id, 'nombre' => 'Agua natural 1L', 'slug' => 'agua-natural-1l', 'descripcion' => 'Agua purificada 1 litro', 'precio' => 18.00]);
        Producto::create(['categoria_id' => $bebidas->id, 'nombre' => 'Jugo de naranja', 'slug' => 'jugo-naranja', 'descripcion' => 'Jugo de naranja natural 500ml', 'precio' => 30.00]);

        // Productos - Postres
        Producto::create(['categoria_id' => $postres->id, 'nombre' => 'Pastel de chocolate', 'slug' => 'pastel-chocolate', 'descripcion' => 'Rebanada de pastel de chocolate', 'precio' => 45.00]);
        Producto::create(['categoria_id' => $postres->id, 'nombre' => 'Flan napolitano', 'slug' => 'flan-napolitano', 'descripcion' => 'Flan napolitano casero', 'precio' => 35.00]);
        Producto::create(['categoria_id' => $postres->id, 'nombre' => 'Helado 2 bolas', 'slug' => 'helado-2-bolas', 'descripcion' => 'Helado de vainilla y chocolate 2 bolas', 'precio' => 40.00]);

        // Productos - Extras
        Producto::create(['categoria_id' => $extras->id, 'nombre' => 'Queso extra', 'slug' => 'queso-extra', 'descripcion' => 'Porción extra de mozzarella', 'precio' => 15.00]);
        Producto::create(['categoria_id' => $extras->id, 'nombre' => 'Pepperoni extra', 'slug' => 'pepperoni-extra', 'descripcion' => 'Porción extra de pepperoni', 'precio' => 15.00]);
        Producto::create(['categoria_id' => $extras->id, 'nombre' => 'Aderezo ranch', 'slug' => 'aderezo-ranch', 'descripcion' => 'Aderezo ranch 50ml', 'precio' => 10.00]);
        Producto::create(['categoria_id' => $extras->id, 'nombre' => 'Salsa chipotle', 'slug' => 'salsa-chipotle', 'descripcion' => 'Salsa chipotle 50ml', 'precio' => 10.00]);

        // Cliente ejemplo
        $cliente = Cliente::create([
            'nombre' => 'Juan Pérez',
            'telefono' => '555-1234-5678',
            'direccion' => 'Calle Principal #123, Colonia Centro',
            'email' => 'juan@example.com',
            'notas' => 'Tocar el timbre 2 veces',
        ]);

        // Pedido ejemplo
        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'subtotal' => 248.00,
            'descuento_puntos' => 0,
            'total' => 248.00,
            'estado' => 'entregado',
            'metodo_pago' => 'efectivo',
            'notas' => 'Sin cebolla por favor',
        ]);

        $producto1 = Producto::where('slug', 'pizza-pepperoni')->first();
        $producto2 = Producto::where('slug', 'coca-cola-600ml')->first();

        PedidoProducto::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto1->id,
            'cantidad' => 2,
            'precio_unitario' => 109.00,
            'subtotal' => 218.00,
        ]);

        PedidoProducto::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto2->id,
            'cantidad' => 1,
            'precio_unitario' => 25.00,
            'subtotal' => 25.00,
        ]);

        Pago::create([
            'pedido_id' => $pedido->id,
            'monto' => 248.00,
            'metodo' => 'efectivo',
            'confirmado' => true,
            'fecha_pago' => now(),
        ]);

        // Puntos por la compra
        Punto::create([
            'cliente_id' => $cliente->id,
            'puntos' => 25,
            'concepto' => 'Compra #1',
            'pedido_id' => $pedido->id,
        ]);

        $cliente->increment('puntos_acumulados', 25);
    }
}
