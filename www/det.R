
#load packages (manually preinstalled)
library("raster")
library("rgdal")
library('leafletR')

#read inputs
readIn <- commandArgs(trailingOnly = TRUE)

in_pShapefile <- readIn[1]
in_fShapefile <- readIn[2]
in_pRaster <- readIn[3]
in_fRaster <- readIn[4]
in_pCache <- readIn[5]
in_fCache <- readIn[6]
in_pBase <- readIn[7]
in_extractType <- readIn[8]

in_bounds <- readIn[9]

#set bounds if values were given for raster
if (in_bounds == "TRUE"){
	in_lowerBound <- as.numeric(readIn[10])
	in_upperBound <- as.numeric(readIn[11])
}

#prepare paths
dir_base <- paste(in_pBase, "/resources/", sep="")
dir_shapefile <- paste(dir_base, in_pShapefile, sep="")
dir_raster <- paste(dir_base, in_pRaster, sep="")
dir_cache <- paste(dir_base, in_pCache, sep="")
dir_geojson <- paste(dir_cache, "/geojsons", sep="")

#load shapefile
setwd(dir_shapefile)
myVector <- readOGR(dir_shapefile, in_fShapefile)

#load raster
setwd(dir_raster)
myRaster <- raster(in_fRaster, crs="+proj=longlat +datum=WGS84 +no_defs")

#remove NA values
myRaster[is.na(myRaster)] <- 0 

#remove values outside bounds
if ( in_bounds == "TRUE" ){
	myRaster[myRaster < in_lowerBound] <- 0
	myRaster[myRaster > in_upperBound] <- 0
}

#extract raster data
if (in_extractType == "sum"){
	myExtract <- extract(myRaster, myVector, fun=sum, sp=TRUE, small=TRUE)
} else {
	myExtract <- extract(disaggregate(myRaster, fact=c(4,4)), myVector, fun=mean, sp=TRUE, weights=TRUE, small=TRUE)
}
myOutput <- myExtract@data

#output to csv
setwd(dir_cache)
write.table(myOutput, in_fCache, quote=T, row.names=F, sep=",")

#create geojson for mapping applications
setwd(dir_geojson)
toGeoJSON(data=myExtract, name=unlist(strsplit(in_fCache, "[.]"))[1])