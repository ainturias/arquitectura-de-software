package Negocio;

import Datos.DProducto;
import java.sql.SQLException;
import java.util.ArrayList;
import javax.swing.table.DefaultTableModel;

/**
 *
 * @author Alvaro
 */
public class NProducto {
    DProducto dp;

    public NProducto() {
        this.dp = new DProducto();
    }
    
    public DefaultTableModel getProductos() throws SQLException {
        return dp.getProductos();
    }
    
    public ArrayList<Object[]> getProductos2() throws SQLException{
        return this.dp.getProductos2();
    }
    
    public boolean crear(String nombre, Double precio, int idCategoria){
        this.dp.setNombre(nombre);
        this.dp.setPrecio(precio);
        this.dp.setIdCategoria(idCategoria);
        return this.dp.crear();
    }
    
    public boolean editar(int id, String nombre, Double precio, int idCategoria) {
        this.dp.setId(id);
        this.dp.setNombre(nombre);
        this.dp.setPrecio(precio);
        this.dp.setIdCategoria(idCategoria);
        return this.dp.editar();
    }
    
    public boolean eliminar(int id) {
        this.dp.setId(id);
        return this.dp.eliminar();
    }
    
    
}
