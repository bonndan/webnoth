/**
 * Webnoth WebGL (three.js) setup.
 * 
 * This script was written by
 * http://www.smartjava.org/content/threejs-render-real-world-terrain-heightmap-using-open-data
 */
var CONS = {
    // THREE.JS CONSTANTS
    // set the scene size
    WIDTH: 234,
    HEIGHT: 396,
    // set some camera attributes
    VIEW_ANGLE: 35,
    NEAR: 0.1,
    FAR: 10000,
    CAMERA_X: 300,
    CAMERA_Y: 200,
    CAMERA_Z: 600
}


if ( ! Detector.webgl ) {
    Detector.addGetWebGLMessage();
    document.getElementById( 'container' ).innerHTML = "";
}

var container, stats;
var camera, controls, scene, renderer, clock;
var mesh, detailTexture, terrain;

var Webnoth = {
    
    /**
     * Creates a renderer inside the given element.
     * 
     * @param {type} elementId
     * @returns {undefined}
     */
    makeRenderer: function(elementId)
    {
        renderer = new THREE.WebGLRenderer();
        //renderer.setSize(CONS.WIDTH, CONS.HEIGHT);
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setClearColor(0x0000cc);
        
        
        renderer.shadowCameraNear = 3;
        renderer.shadowCameraFar = CONS.FAR;
        renderer.shadowCameraFov = 50;

        renderer.shadowMapBias = 0.0039;
        //renderer.shadowMapDarkness = 0.5;
        renderer.shadowMapDarkness = 1.0;
        renderer.shadowMapWidth = CONS.WIDTH;
        renderer.shadowMapHeight = CONS.HEIGHT;

        renderer.shadowMapEnabled = true;
        renderer.shadowMapSoft = true;
        
        container = document.getElementById(elementId);
        container.innerHTML = "";
        container.appendChild(renderer.domElement);
    },
          
    /**
     * Appends the statistics to the given element.
     * 
     * @param {string} elementId
     * @returns {undefined}
     */
    appendStats: function(elementId)
    {
        stats = new Stats();
        stats.domElement.style.position = 'absolute';
        stats.domElement.style.top = '0px';
        container = document.getElementById(elementId);
        container.appendChild(stats.domElement);
    },
            
    /**
     * Creates a camera which looks at the scene
     * @param {type} scene
     * @returns {undefined}
     */
    makeCamera: function(scene)
    {
        camera = new THREE.PerspectiveCamera(CONS.VIEW_ANGLE, window.innerWidth / window.innerHeight, CONS.NEAR, CONS.FAR);
        camera.position.z = CONS.CAMERA_Z;
        camera.position.x = CONS.CAMERA_X;
        camera.position.y = CONS.CAMERA_Y;
        camera.lookAt(scene.position);
    },
    
    /**
     * Adds first person controls.
     * 
     * @param {type} camera
     * @returns {undefined}
     */
    makeControls: function(camera)
    {
        controls = new THREE.FirstPersonControls(camera);
        controls.movementSpeed = 1000;
        controls.lookSpeed = 0.1;
    },
    
    /**
     * Creates a directional light source.
     * 
     * @returns {Webnoth.makeSun.sun}
     */
    makeSun: function()
    {
        var sunPosition;
        sunPosition = new THREE.Vector3(1000, 800, 400);

        var sun = new THREE.DirectionalLight(0xffffff);
        sun.castShadow = true;
        sun.shadowCameraVisible = true; //set true to see shadow frustum
        sun.intensity = 3.8;
        sun.position.set(sunPosition.x, sunPosition.y, sunPosition.z);
        sun.shadowCameraNear = 100;
        sun.shadowCameraFar = 2500;
        sun.shadowBias = 0.0001;
        sun.shadowDarkness = 0.35;
        //sun.shadowMapWidth = 1024; //512px by default
        //sun.shadowMapHeight = 1024; //512px by default
        
        return sun;
    }
}           


init();

/**
 * Setup the scene, camera etc.
 * 
 * @returns {undefined}
 */
function init() {

    clock = new THREE.Clock();
    scene = new THREE.Scene();
    Webnoth.makeCamera(scene);
    scene.add(camera);
    Webnoth.makeControls(camera);
    scene.add(Webnoth.makeSun());
    
    Webnoth.makeRenderer('container');
    Webnoth.appendStats('container');

    window.addEventListener('resize', onWindowResize, false);
    loadTextures();
}

/**
 * Loads the detail texture.
 * 
 * @returns {undefined}
 */
function loadTextures() {
    detailTexture = THREE.ImageUtils.loadTexture("cache/2_Tutorial.png", null, loadShadowTextures);
}

/**
 * Loads the heightmap, calculates the shadow map, then calls initMesh
 * 
 * @returns {undefined}
 */
function loadShadowTextures() {

    var heightmap = new Image();
    heightmap.src = "cache/2_Tutorial.heightmap.png";
    heightmap.onload = function() {
        var heightData = readHeight(heightmap, CONS.WIDTH, CONS.HEIGHT);
        shadowTexture = makeShadowTexture(heightData, CONS.WIDTH, CONS.HEIGHT);
        initMesh(heightData, heightmap);
    }
}



/**
 * Creates the mesh after all textures are ready.
 * 
 * @param {type} heightData
 * @returns {undefined}
 */
function initMesh(heightData, heightmap)
{
    // the following configuration defines how the terrain is rendered
    var terrainShader = THREE.ShaderTerrain[ "terrain" ];
    var uniformsTerrain = THREE.UniformsUtils.clone(terrainShader.uniforms);

    // how to treat and scale the normal texture
    uniformsTerrain[ "tNormal" ].value = false;
    uniformsTerrain[ "uNormalScale" ].value = false;
    
    //shadow map
    uniformsTerrain[ "tLightmap" ].value = shadowTexture;

    // the displacement determines the height of a vector, mapped to
    // the heightmap
    uniformsTerrain[ "tDisplacement" ].value = heightmap;
    uniformsTerrain[ "uDisplacementScale" ].value = 100;

    // the following textures can be use to finetune how
    // the map is shown. These are good defaults for simple
    // rendering
    //uniformsTerrain[ "tDiffuse1" ].value = detailTexture;
    uniformsTerrain[ "tDetail" ].value = detailTexture;
    //uniformsTerrain[ "enableDiffuse1" ].value = true;
    //uniformsTerrain[ "enableDiffuse2" ].value = true;
    uniformsTerrain[ "enableSpecular" ].value = false;

    // diffuse is based on the light reflection
    uniformsTerrain[ "uDiffuseColor" ].value.setHex(0xeeeeee);
    uniformsTerrain[ "uSpecularColor" ].value.setHex(0x000000);
    // is the base color of the terrain
    uniformsTerrain[ "uAmbientColor" ].value.setHex(0xffffff);

    // how shiny is the terrain
    uniformsTerrain[ "uShininess" ].value = 1.0;

    // handles light reflection
    uniformsTerrain[ "uRepeatOverlay" ].value.set(0, 0);

    // configure the material that reflects our terrain
    var material = new THREE.ShaderMaterial({
        uniforms: uniformsTerrain,
        vertexShader: terrainShader.vertexShader,
        fragmentShader: terrainShader.fragmentShader,
        lights: true,
        fog: false
    });

    
    var geometry = new THREE.PlaneGeometry(CONS.WIDTH, CONS.HEIGHT, CONS.WIDTH - 1, CONS.HEIGHT - 1);
    geometry.applyMatrix(new THREE.Matrix4().makeRotationX(-Math.PI / 2));

    for (var i = 0, l = geometry.vertices.length; i < l; i++) {
        geometry.vertices[ i ].y = heightData[ i ] * 10;
    }
    
    geometry.computeFaceNormals();
    geometry.computeVertexNormals();
    geometry.computeTangents();

    // create a 3D object to add
    terrain = new THREE.Mesh(geometry, material);
    terrain.position.set(0, 0, 0);
    terrain.rotation.set(degrees(-90), 0, 0);
    terrain.castShadow = true;
    terrain.receiveShadow = true;
    
    scene.add(terrain);
    
    camera.position.y = heightData[ CONS.WIDTH / 2 + CONS.HEIGHT / 2 * CONS.WIDTH ] + 150;
    animate();
}

/**
 * Starts rendering, calls render() on each frame, updates the stats
 * @returns {undefined}
 */
function animate() {
    requestAnimationFrame( animate );
    render();
    stats.update();
}

/**
 * Renders the scene.
 * 
 * @returns {undefined}
 */
function render() {
    controls.update( clock.getDelta() );
    renderer.render( scene, camera );
}


//https://github.com/mrdoob/three.js/issues/1135
//
function degrees(degrees) {
    radians = degrees * (Math.PI / 180);
    return radians;
}

/**
 * 
 * @returns {undefined}
 */
function onWindowResize() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
    controls.handleResize();
}

/**
 * read heights from an image
 * 
 * @param img
 * @param width
 * @param height
 * @return Float32Array
 */
function readHeight(img, width, height)
{
    var size = width * height, data = new Float32Array(size);

    var canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    var context = canvas.getContext('2d');
    context.drawImage(img, 0, 0);

    var imgd = context.getImageData(0, 0, width, height);
    var pix = imgd.data;

    var j = 0;
    for (var i = 0, n = pix.length; i < n; i += (4)) {
        data[j++] = (pix[i] + pix[i + 1] + pix[i + 2]) / 30;
    }

    return data;
}


/**
 * Texture related functions
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */


/**
 * Generates height based on noise
 * 
 * @return Float32Array
 */
function generateNoiseHeight(width, height) {

    var size = width * height, data = new Float32Array(size),
            perlin = new ImprovedNoise(), quality = 1, z = Math.random() * 100;

    for (var i = 0; i < size; i++) {

        data[ i ] = 0

    }

    for (var j = 0; j < 4; j++) {

        for (var i = 0; i < size; i++) {

            var x = i % width, y = ~~(i / width);
            data[ i ] += Math.abs(perlin.noise(x / quality, y / quality, z) * quality * 1.75);


        }

        quality *= 5;

    }

    return data;
}

/**
 * generates the height texture with shadows
 */
function generateShadowmap(data, width, height) {

    var canvas, canvasScaled, context, image, imageData,
            level, diff, vector3, sun, shade;

    vector3 = new THREE.Vector3(0, 0, 0);

    sun = new THREE.Vector3(1, 1, 1);
    sun.normalize();

    canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;

    context = canvas.getContext('2d');
    context.fillStyle = '#000';
    context.fillRect(0, 0, width, height);

    image = context.getImageData(0, 0, canvas.width, canvas.height);
    imageData = image.data;

    for (var i = 0, j = 0, l = imageData.length; i < l; i += 4, j++) {

        vector3.x = data[ j - 2 ] - data[ j + 2 ];
        vector3.y = 2;
        vector3.z = data[ j - width * 2 ] - data[ j + width * 2 ];
        vector3.normalize();

        shade = vector3.dot(sun);

        var colorValue = (shade * 255) * (0.5 + data[ j ] * 0.07);
        imageData[ i ] = colorValue;
        imageData[ i + 1 ] = colorValue;
        imageData[ i + 2 ] = colorValue;
    }

    context.putImageData(image, 0, 0);

    // Scaled 4x

    canvasScaled = document.createElement('canvas');
    canvasScaled.width = width * 4;
    canvasScaled.height = height * 4;

    context = canvasScaled.getContext('2d');
    context.scale(4, 4);
    context.drawImage(canvas, 0, 0);

    image = context.getImageData(0, 0, canvasScaled.width, canvasScaled.height);
    imageData = image.data;

    for (var i = 0, l = imageData.length; i < l; i += 4) {

        var v = ~~(Math.random() * 5);

        imageData[ i ] += v;
        imageData[ i + 1 ] += v;
        imageData[ i + 2 ] += v;

    }

    context.putImageData(image, 0, 0);
    return canvasScaled;
}

/**
 * Generates a shadow texture and assigns it to the shadowTexture global variable.
 * 
 * @param heightData
 * @param {int} width 
 * @param {int} height 
 * @returns THREE.Texture
 */
function makeShadowTexture(heightData, width, height)
{
    var shadowTexture = new THREE.Texture(
        generateShadowmap(heightData, width, height),
        new THREE.UVMapping(),
        THREE.ClampToEdgeWrapping,
        THREE.ClampToEdgeWrapping
    );
    shadowTexture.needsUpdate = true;
    
    return shadowTexture;
}