library('leafletR')
library('rgdal')

readIn <- commandArgs(trailingOnly = TRUE)

dir_base <- readIn[1]
in_pShapefile <- readIn[2]
in_fShapefile <- readIn[3]
in_pLeaf <- readIn[4]


dir_shapefile <- paste(dir_base, in_pShapefile, sep="")

setwd(dir_shapefile)

myVector <- readOGR(dir_shapefile, in_fShapefile)


dir_leaflet <- paste(dir_base, in_pLeaf, sep="") 

# setwd(dir_leaflet)

myLeaf <- toGeoJSON(data=myVector, name="Leaflet", dest=dir_leaflet)


# myStyle <- styleSingle(col=1, lwd=1, alpha=1)

# myMap <- leaflet(	data=myLeaf,
# 	            	title="Leaflet", 
# 					base.map="osm",
# 					style=myStyle,
# 					incl.data=FALSE
# 				)