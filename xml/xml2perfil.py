# # -*- coding: utf-8 -*-
"""
@version 1.0 23/Octubre/2024
@author: David Covián Gómez
"""

import xml.etree.ElementTree as ET

class Svg(object):
    """
        Genera archivo SVG
        @version 1.1 23/Octubre/2024
    """
    def __init__(self):
        self.raiz = ET.Element('svg', xmlns="http://www.w3.org/2000/svg", version="2.0", width="1000", height="600", viewBox="500 -1000 6000 2000")
        self.doc = self.raiz

    """
        Añade la polilinea y sus points y style
        @version 1.1 23/Octubre/2024
    """
    def addPolyline(self,distanciaYAltitud):
        pl = ET.SubElement(self.doc,'polyline', points=distanciaYAltitud, style="fill:white;stroke:red;stroke-width:4")
    """
        Añade un texto a cada coordenada
        @version 1.1 23/Octubre/2024
    """
    def addText(self,x,y,name):
        t = ET.SubElement(self.doc,'text', x=x, y=y, style="writing-mode: tb; glyph-orientation-vertical: 0; font-size: 2em;").text = name
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
def leerAltitudYDistancia(xml_file):
    
    tree = ET.parse(xml_file)
    root = tree.getroot()

    ns = {'ns': 'http://www.uniovi.es'}

    result = ""
    for tramo in root.findall('ns:tramos/ns:tramo', ns):
        distancia = tramo.find('ns:distancia', ns).text
        altitud = tramo.find('ns:altitudTramo', ns).text
        result+=(distancia+","+str(float(altitud)*100)+"\n")

    return result

def leerDatosDeText(xml_file):
    tree = ET.parse(xml_file)
    root = tree.getroot()

    ns = {'ns': 'http://www.uniovi.es'}
    i = 0
    result = []

    for tramo in root.findall('ns:tramos/ns:tramo', ns):
        cadenaDeElementos = "" # "x:y:name"
        distancia = tramo.find('ns:distancia', ns).text
        altitud = tramo.find('ns:altitudTramo', ns).text
        i += 1
        result.append(distancia+":"+str(float(altitud)*100)+":"+"Tramo "+str(i))
    return result


def main():
    print(Svg.__doc__)

    nombreKML = "altimetria.svg"
    nuevoKML = Svg()
    xmlFile = input('Introduzca un archivo XML = ')
    
    altitudYDistancia = leerAltitudYDistancia(xmlFile)
    nuevoKML.addPolyline(altitudYDistancia)
    
    datosText = leerDatosDeText(xmlFile)
    
    for i in range (0,len(datosText)):
        datos = datosText[i].split(":")
        x = datos[0]
        y = datos[1]
        name = datos[2]
        nuevoKML.addText(x,y,name)
    ## IMPORTANTE
    ## Como los valores de la altitud son muy pequeños, los multiplicamos por una constante de 100 para que se puedan visualizar mejor
    nuevoKML.escribir(nombreKML)
    print("Creado el archivo: ", nombreKML)

if __name__ == "__main__":
    main()
