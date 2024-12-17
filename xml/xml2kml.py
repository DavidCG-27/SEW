# # -*- coding: utf-8 -*-
"""
@version 1.0 23/Octubre/2024
@author: David Covián Gómez
@author: Juan Manuel Cueva Lovelle. Universidad de Oviedo
"""

import xml.etree.ElementTree as ET

class Kml(object):
    """
        Genera archivo KML con puntos y líneas
        @version 1.1 23/Octubre/2024
    """
    def __init__(self):
        self.raiz = ET.Element('kml', xmlns="http://www.opengis.net/kml/2.2")
        self.doc = ET.SubElement(self.raiz,'Document')

    """
        Añade un elemento <Placemark> con puntos <Point>
        @version 1.1 23/Octubre/2024
    """
    def addPlacemark(self,nombre,descripcion,long,lat,alt, modoAltitud):

        pm = ET.SubElement(self.doc,'Placemark')
        ET.SubElement(pm,'name').text = '\n' + nombre + '\n'
        ET.SubElement(pm,'description').text = '\n' + descripcion + '\n'
        punto = ET.SubElement(pm,'Point')
        ET.SubElement(punto,'coordinates').text = '\n{},{},{}\n'.format(long,lat,alt)
        ET.SubElement(punto,'altitudeMode').text = '\n' + modoAltitud + '\n'

    """
        Añade un elemento <Placemark> con líneas <LineString>
        @version 1.1 23/Octubre/2024
    """
    def addLineString(self,nombre,extrude,tesela, listaCoordenadas, modoAltitud, color, ancho):
        
        ET.SubElement(self.doc,'name').text = '\n' + nombre + '\n'
        pm = ET.SubElement(self.doc,'Placemark')
        ls = ET.SubElement(pm, 'LineString')
        ET.SubElement(ls,'extrude').text = '\n' + extrude + '\n'
        ET.SubElement(ls,'tessellation').text = '\n' + tesela + '\n'
        ET.SubElement(ls,'coordinates').text = '\n' + listaCoordenadas + '\n'
        ET.SubElement(ls,'altitudeMode').text = '\n' + modoAltitud + '\n'

        estilo = ET.SubElement(pm, 'Style')
        linea = ET.SubElement(estilo, 'LineStyle')
        ET.SubElement (linea, 'color').text = '\n' + color + '\n'
        ET.SubElement (linea, 'width').text = '\n' + ancho + '\n'
    """
        Escribe el archivo KML con declaración y codificación
        @version 1.1 23/Octubre/2024
    """
    def escribir(self,nombreArchivoKML):
        
        arbol = ET.ElementTree(self.raiz)
        arbol.write(nombreArchivoKML, encoding='utf-8', xml_declaration=True)
"""
    Lee las coordenadas de los tramos desde un archivo XML y las devuelve en formato KML
    @version 1.1 23/Octubre/2024
"""
def leerCoordenadasTramos(xml_file):
    
    tree = ET.parse(xml_file)
    root = tree.getroot()

    ns = {'ns': 'http://www.uniovi.es'}

    coordenadas = []
    for tramo in root.findall('ns:tramos/ns:tramo', ns):
        latitud = tramo.find('ns:latitudTramo', ns).text
        longitud = tramo.find('ns:longitudTramos', ns).text
        altitud = tramo.find('ns:altitudTramo', ns).text
        coordenadas.append(f'{longitud},{latitud},{altitud}')

    return '\n'.join(coordenadas)

def main():
    print(Kml.__doc__)

    nombreKML = "circuito.kml"
    nuevoKML = Kml()
    coordenadasTramos = leerCoordenadasTramos(input('Introduzca un archivo XML = '))
    nuevoKML.addLineString("Circuito Bakú", "1", "1",
                           coordenadasTramos, 'relativeToGround',
                           '#ff0000ff', "5")
    nuevoKML.escribir(nombreKML)
    print("Creado el archivo: ", nombreKML)

if __name__ == "__main__":
    main()
