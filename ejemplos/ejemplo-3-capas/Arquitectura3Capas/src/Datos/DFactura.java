package Datos;

import Database.Conexion;
import java.sql.Connection;
import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

/**
 *
 * @author Alvaro
 */
public class DFactura {
    int id;
    Date fecha;
    double monto;
    int nit;
    String nombre;
    Connection con;
    PreparedStatement ps;
    ResultSet rs;

    public DFactura() {
        
        Conexion conectar = new Conexion();
        con = conectar.getConnection();
    }

    public DFactura(int id, Date fecha, double monto, int nit, String nombre) {
        this.id = id;
        this.fecha = fecha;
        this.monto = monto;
        this.nit = nit;
        this.nombre = nombre;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public Date getFecha() {
        return fecha;
    }

    public void setFecha(Date fecha) {
        this.fecha = fecha;
    }

    public double getMonto() {
        return monto;
    }

    public void setMonto(double monto) {
        this.monto = monto;
    }

    public int getNit() {
        return nit;
    }

    public void setNit(int nit) {
        this.nit = nit;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }
    
    public DefaultTableModel getFacturas() throws SQLException {
        String query = "select * from factura";
        ps = con.prepareStatement(query);
        rs = ps.executeQuery();
        DefaultTableModel table = new DefaultTableModel();
        try {        
            table.setColumnIdentifiers(new String[]{"ID", "FECHA", "MONTO", "NIT", "NOMBRE"});
            Object[] tableData = new Object[table.getColumnCount()];
            while (rs.next()) {
                tableData[0] = rs.getInt("id");
                tableData[1] = rs.getString("fecha");
                tableData[2] = rs.getDouble("monto");
                tableData[3] = rs.getInt("nit");
                tableData[4] = rs.getString("nombre");
                table.addRow(tableData);
            }
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al listar " + e, "Error", JOptionPane.ERROR_MESSAGE);
            return null;
        }
        return table;
    }   
    
    
    public boolean crearFactura() {
        String query = "insert into factura (id, fecha, monto, nit, nombre) values (?, ?, ?, ?, ?)";
        try {
            ps = con.prepareStatement(query);
            ps.setInt(1,this.id);
            ps.setDate(2, this.fecha);
            ps.setDouble(3, this.monto);
            ps.setInt(4, this.nit);
            ps.setString(5, this.nombre);
            ps.execute();
            ps.close();
            return true;
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al insertar la factura en la base de datos" + e, "Error", JOptionPane.ERROR_MESSAGE);
        }
        return false;
    }
    
    public boolean eliminarFactura() {
        String query = "delete from factura where id = ? ";
        try {
            PreparedStatement ps = con.prepareStatement(query);
            ps.setInt(1, this.id);
            ps.execute();
            ps.close();
            return true;
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al eliminar la factura de la base de datos" + e, "Error", JOptionPane.ERROR_MESSAGE);
        }
        return false;
    }
     
    
    
    
}
