package Negocio;

import Datos.DDetalleFactura;
import Datos.DFactura;
import java.sql.Date;
import java.sql.SQLException;
import java.util.ArrayList;
import javax.swing.table.DefaultTableModel;

/**
 *
 * @author Alvaro
 */
public class NFactura {
    DFactura df;
    ArrayList<DDetalleFactura> ddf;

    public NFactura() {
        this.df = new DFactura();   
        this.ddf = new ArrayList<DDetalleFactura>();
    }
    
    
    public DefaultTableModel getFacturas() throws SQLException {
        return df.getFacturas();
    }    
    
    public void crearFactura(int id, Date fecha, double monto, int nit, String nombre, ArrayList<Object[]> detalles) throws Exception {
        this.df.setId(id);
        this.df.setFecha(fecha);
        this.df.setMonto(monto);
        this.df.setNit(nit);
        this.df.setNombre(nombre);
        this.df.crearFactura();  
        for (Object[] e : detalles){
            this.addDetalleFactura((int) e[0], (int) e[1], (double) e[2], (int) e[3]);
        }
        
    }       
      
    public boolean eliminarFactura(int id) {
        this.df.setId(id);
        return this.df.eliminarFactura();
    }
       
    /*public boolean crearDetalleFactura(int idFactura, int idProducto, double precio, int cantidad) {
        this.ddf.setIdFactura(idFactura);
        this.ddf.setIdProducto(idProducto);
        this.ddf.setPrecio(precio);
        this.ddf.setCantidad(cantidad);
        return this.ddf.crearDetalleFactura();
    }
    */
    
    public void addDetalleFactura(int idFactura, int idProducto, double precio, int cantidad){
        DDetalleFactura detalleFactura = new DDetalleFactura(idFactura, idProducto, precio, cantidad);
        this.ddf.add(detalleFactura);
        int aux = this.ddf.size();
        this.ddf.get(aux-1).crearDetalleFactura();
    }  
}
