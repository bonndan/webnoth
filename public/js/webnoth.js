/**
 * Webnoth WebGL (three.js) setup.
 * 
 * This script was written by
 * http://www.smartjava.org/content/threejs-render-real-world-terrain-heightmap-using-open-data
 */
var CONS = {
    // THREE.JS CONSTANTS
    // set the scene size
    WIDTH:904,
    HEIGHT:604,
 
    // set some camera attributes
    VIEW_ANGLE:45,
    NEAR:0.1,
    FAR:10000,
 
    CAMERA_X:300,
    CAMERA_Y:300,
    CAMERA_Z:500
}
 
var scene = {};
var renderer = {};
var camera = {};
var controls;
 
 
var n = 0;
initMap();
 
// Wait until everything is loaded before continuing
function loaded() {
    n++;
    console.log("loaded: " + n);
 
    if (n == 3) {
        terrain.visible = true;
        console.log('ff');
        render();
    }
}
 
function initMap() {
 
    // setup default three.js stuff
    renderer = new THREE.WebGLRenderer();
    renderer.setSize(CONS.WIDTH, CONS.HEIGHT);
    renderer.setClearColor(0x0000cc);
    $("#main_map").append(renderer.domElement);
 
    camera = new THREE.PerspectiveCamera(CONS.VIEW_ANGLE, CONS.WIDTH / CONS.HEIGHT, CONS.NEAR, CONS.FAR);
    scene = new THREE.Scene();
    scene.add(camera);
 
    camera.position.z = CONS.CAMERA_Z;
    camera.position.x = CONS.CAMERA_X;
    camera.position.y = CONS.CAMERA_Y;
    camera.lookAt(scene.position);
 
    // add a light
    pointLight = new THREE.PointLight(0xFFFFFF);
    scene.add(pointLight);
    pointLight.position.x = 1000;
    pointLight.position.y = 1000;
    pointLight.position.z = 1000;
    pointLight.intensity = 4.6;
 
 
    // load the heightmap we created as a texture
    var heightmap = THREE.ImageUtils.loadTexture('cache/2_Tutorial.heightmap.png', null, loaded);
 
    // load two other textures we'll use to make the map look more real
    var detailTexture = THREE.ImageUtils.loadTexture("cache/2_Tutorial.png", null, loaded);
 
    // the following configuration defines how the terrain is rendered
    var terrainShader = THREE.ShaderTerrain[ "terrain" ];
    var uniformsTerrain = THREE.UniformsUtils.clone(terrainShader.uniforms);
 
    // how to treat abd scale the normal texture
    uniformsTerrain[ "tNormal" ].value = detailTexture;
    uniformsTerrain[ "uNormalScale" ].value = 1.2;
 
    // the displacement determines the height of a vector, mapped to
    // the heightmap
    uniformsTerrain[ "tDisplacement" ].value = heightmap;
    uniformsTerrain[ "uDisplacementScale" ].value = 20;
 
    // the following textures can be use to finetune how
    // the map is shown. These are good defaults for simple
    // rendering
    uniformsTerrain[ "tDiffuse1" ].value = detailTexture;
    uniformsTerrain[ "tDetail" ].value = detailTexture;
    //uniformsTerrain[ "enableDiffuse1" ].value = true;
    //uniformsTerrain[ "enableDiffuse2" ].value = true;
    uniformsTerrain[ "enableSpecular" ].value = true;
 
    // diffuse is based on the light reflection
    uniformsTerrain[ "uDiffuseColor" ].value.setHex(0xeeeeee);
    uniformsTerrain[ "uSpecularColor" ].value.setHex(0x000000);
    // is the base color of the terrain
    uniformsTerrain[ "uAmbientColor" ].value.setHex(0xffffff);
 
    // how shiny is the terrain
    uniformsTerrain[ "uShininess" ].value = 0.5;
 
    // handles light reflection
    uniformsTerrain[ "uRepeatOverlay" ].value.set(0, 0);
 
    // configure the material that reflects our terrain
    var material = new THREE.ShaderMaterial({
        uniforms:       uniformsTerrain,
        vertexShader:   terrainShader.vertexShader,
        fragmentShader: terrainShader.fragmentShader,
        lights:         true,
        fog:            false
    });
 
    // we use a plain to render as terrain
    var geometryTerrain = new THREE.PlaneGeometry(234, 324, 32, 32);
    geometryTerrain.computeFaceNormals();
    geometryTerrain.computeVertexNormals();
    geometryTerrain.computeTangents();
 
    // create a 3D object to add
    terrain = new THREE.Mesh(geometryTerrain, material);
    terrain.position.set(0, -125, 0);
    terrain.rotation.x = -Math.PI / 2;
 
    // add the terrain
    scene.add(terrain);
 
    // tell everything is ready
    loaded();
}
 
// render the scene
function render() {
    renderer.render(scene, camera);
}