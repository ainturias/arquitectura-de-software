package Negocio;

import Datos.DCategoria;
import java.sql.SQLException;
import java.util.ArrayList;
import javax.swing.table.DefaultTableModel;

/**
 *
 * @author Alvaro
 */
public class NCategoria {
    DCategoria dc;

    public NCategoria() {
        this.dc = new DCategoria();
    }
    
    public DefaultTableModel getCategorias() throws SQLException{
        return dc.getCategorias();
    }
    
    public ArrayList<Object[]> getCategorias2() throws SQLException{
        return this.dc.getCategorias2();
    }
    
    public boolean crear(String nombre){
        this.dc.setNombre(nombre);
        return this.dc.crear();
    }
    
    public boolean editar(int id, String nombre) { 
        this.dc.setId(id);
        this.dc.setNombre(nombre);
        return this.dc.editar();
        
    }
    
    public boolean eliminar(int id) {
        this.dc.setId(id);
        return this.dc.eliminar();
    }
    
    
}
